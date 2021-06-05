<?php

namespace App\Controller;

use DateTime;
use App\Entity\Notification;
use App\Entity\ServerEndpoint;
use App\Service\CallApiService;
use App\Entity\MercureNotifications;
use App\Repository\LDSMapRepository;
use App\Repository\MapLDSRepository;
use Symfony\Component\Mercure\Update;
use App\Repository\NotificationRepository;
use Symfony\Component\Mercure\HubInterface;
use App\Repository\ServerEndpointRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
       /**
     * @Route("/AdminDashboard", name="AdminDashboard")
     */
    public function index(CallApiService $callapiservice, ServerEndpointRepository $sp,MapLDSRepository $mapRep ,Request $request): Response
    {

      // dd($this->security->getUser());
       /* $user = $this->get('security.token_storage')->getToken()->getUser();
        $user->getUsername();
        dd($user);*/
        $teamsName = [];
        $teamsURL = [];

        $projects = [];
        $commitNumber = 0 ;
        $mergesNumber = 0 ;

        $releasesNumber = 0 ;
        $em = $this->getDoctrine()->getManager();
        $teams = $em->getRepository('App\Entity\ServerEndpoint')->findAll();
        $locations =  $mapRep -> findAll();
       // dd($notificationss);
       // $teams =  $sp -> findTeamByType($type);
        foreach ($teams as $team) {
           
            foreach ($locations as $loc) {
                 if ($team -> getMap() == $loc) {
                      
                        $projects= $callapiservice -> GetGitLabProjects($team-> getGitlabURL(), $team -> getToken());
                        foreach ($projects as $proj) {
                            $commitNumber = $commitNumber + count($callapiservice -> GetProjectCommits($proj['id'], $team -> getGitlabURL(), $team -> getToken()));
                            $releasesNumber = $releasesNumber + count($callapiservice -> GetProjectReleases($proj['id'], $team -> getGitlabURL(), $team -> getToken()));
                            $mergesNumber = $mergesNumber + count($callapiservice -> GetProjectMergeReq($proj['id'], $team -> getGitlabURL(), $team -> getToken()));

                        }                   
                         $loc->commits = $commitNumber;
                        $loc->releases = $releasesNumber;
                        $loc->merges = $mergesNumber;
                         $loc->teamName = $team -> getTeam();
                         $mergesNumber=0;

                        $releasesNumber = 0 ;
                         $commitNumber = 0 ;
                }
            }
        
        } 
        
        foreach ($teams as $name){
            array_push($teamsName,$name->getTeam());
            array_push($teamsURL,$name ->getGitlabURL());
        }

       
       // dd($notificationss);
        //$urlServer = array_unique($uniqueServer);
        //dd($urlServer,$teamsName);
        return $this->render('admin/dashboard.html.twig', [
            'teams' => $teams,   
            'names' => json_encode($teamsName),
            'urls' => json_encode($teamsURL),
            'locations' => json_encode($locations), 
        ]);
        
    }
    /**
     * @Route("/getNotif", name="getNotif")
     */
    public function getNotif (NotificationRepository $p): Response{
        // $em = $this->getDoctrine()->getManager();
        // $notificationss = $em->getRepository('App\Entity\Notification')->findByExampleField();
         $notificationss = $p->findByExampleField();
 
         return $this -> json([
             'notificationss' => $notificationss  
         ], 200);
     }
    ////////////////////Filtre

     /**
     * @Route("/loadInstant", name="loadInstant")
     */
    public function getInstantLoad (ServerEndpointRepository $sp, CallApiService $callapiservice, Request $request): Response{
        $search = $request->get('ids');
       
        $tabToReturn = [];
        $tabToReturnGood = [];
        $projects = [];
        $teamsToHandle = [];
        $em = $this->getDoctrine()->getManager();
        $teams = $em->getRepository('App\Entity\ServerEndpoint')->findAll();
       /* foreach($teams as $team){
            foreach($search as $s){
                if ($s == $team-> getGitlabURL()){
                    $projects = $callapiservice -> GetGitLabProjects($team-> getGitlabURL(), $team -> getToken());
                    array_push($tabToReturn, array($team-> getTeam() => $projects));
                }
            
            }
        }*/
       

        foreach($search as $s){
            $teamsToHandle = $sp-> findByUrl($s);
            foreach($teamsToHandle as $team){
                array_push($projects, $callapiservice -> GetGitLabProjects($team-> getGitlabURL(), $team -> getToken()));
            }
            array_push($tabToReturn, array($s => $projects));
        }
        
        return $this -> json([
            
            'html' => $this-> renderView('admin/adminLoadDash.html.twig',['tabToReturn' => $tabToReturn, 'teams' => $teamsToHandle])
        ], 200);
    }

        /**
     * @Route("/loadAjaxInstant", name="loadAjaxInstant")
     */
    public function loadAjaxInstant (CallApiService $callapiservice, ServerEndpointRepository $sp,MapLDSRepository $mapRep ,Request $request): Response{
        $projects = [];
        $commitNumber = 0 ;
        $teamsName = [];
        $em = $this->getDoctrine()->getManager();
        $teams = $em->getRepository('App\Entity\ServerEndpoint')->findAll();
        $locations =  $mapRep -> findAll();
        $releasesNumber = 0 ;
        $mergeReqNumber = 0 ; 
        foreach ($teams as $name){
            array_push($teamsName,$name->getTeam());
        }
        foreach ($teams as $team) {
            foreach ($locations as $loc) {
                 if ($team -> getMap() == $loc) {
                        $projects= $callapiservice -> GetGitLabProjects($team-> getGitlabURL(), $team -> getToken());
                        foreach ($projects as $proj) {
                            $commitNumber = $commitNumber + count($callapiservice -> GetProjectCommits($proj['id'], $team -> getGitlabURL(), $team -> getToken()));
                            $releasesNumber = $releasesNumber + count($callapiservice -> GetProjectReleases($proj['id'], $team -> getGitlabURL(), $team -> getToken()));
                            $mergeReqNumber = $mergeReqNumber + count($callapiservice -> GetProjectMergeReq($proj['id'], $team -> getGitlabURL(), $team -> getToken()));
                        }                   
                         $loc->commits = $commitNumber;
                         $loc->releases = $releasesNumber;
                         $loc->merges = $mergeReqNumber;
                         $releasesNumber = 0 ;
                         $commitNumber = 0 ;
                         $mergeReqNumber =0;

                }
            }
        
        } 
       
        return $this -> json([
            'locations' => json_encode($locations), 
        ], 200);
    }

     

    public function addTeam(ServerEndpointRepository $serverRep,Request $request):Response
    {
      
        $team = new ServerEndpoint();
        if ($request->getMethod() == 'POST') {
            $s1 = $request->get('team');
            $s2 = $request->get('gitlabURL');
            $s3 = $request->get('token');
            $s4 = $request -> get('gitType');
            $team -> setTeam($s1);
            $team -> setGitlabURL($s2);
            $team -> setToken($s3);
            $team -> setGitType($s4);
            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();
            $teams =  $serverRep -> findAll();
            return $this -> json([
                'teams' => $teams,
            ], 200);  
    
        }
                $teams =  $serverRep -> findAll();
               
            return $this -> json([
                'teams' => $teams,
            ], 200);
     
    }

    //Mercure Hub
    /**
     * @Route("/pushMercureNotif", name="pushMercureNotif")
     */
    public function pushMercureNotif(CallApiService $callapiservice): Response
    {
        $notif = new MercureNotifications();
        $em = $this->getDoctrine()->getManager();
        $teams = $em->getRepository('App\Entity\ServerEndpoint')->findAll();

        foreach ($teams as $team){
            $projects = $callapiservice -> GetGitLabProjects($team-> getGitlabURL(), $team -> getToken());
            foreach ($projects as $project){
                $notif = new MercureNotifications();
                $notif -> setIdProject($project['id']);
                $notif -> setNbCommits(count($callapiservice -> GetProjectCommits($project['id'], $team -> getGitlabURL(), $team -> getToken())));
                $notif -> setNbMerges(count ($callapiservice -> GetProjectMergeReq($project['id'], $team -> getGitlabURL(), $team -> getToken())));
                $notif -> setNbReleases(count($callapiservice -> GetProjectReleases($project['id'], $team -> getGitlabURL(), $team -> getToken())));
                $notif -> setNbJobs(count($callapiservice -> GetPipelineJobs($project['id'], $team -> getGitlabURL(), $team -> getToken())));
                $notif -> setNbPipes(count($callapiservice -> GetProjectPipelines($project['id'], $team -> getGitlabURL(), $team -> getToken())));
                $notif -> setNbIssues(count($callapiservice -> GetProjectIssues($project['id'], $team -> getGitlabURL(), $team -> getToken())));
                $em->persist($notif);
                $em->flush();
            }
        }
        return new Response('publishedToTestELSABEN FEL BASE!');
    }

   /**
     * @Route("/test/test", name="testtest")
     */
    public function invokeMercure(HubInterface $hub,CallApiService $callapiservice): Response
    {   
        $em = $this->getDoctrine()->getManager();
        $teams = $em->getRepository('App\Entity\ServerEndpoint')->findAll();
        $notifs = $em->getRepository('App\Entity\MercureNotifications')->findAll();
        foreach($teams as $team){
            $projects = $callapiservice -> GetGitLabProjects($team-> getGitlabURL(), $team -> getToken());
            foreach ($projects as $project){
                foreach($notifs as $notif){
                    $releases = $callapiservice -> GetProjectReleases($project['id'], $team -> getGitlabURL(), $team -> getToken());
                    $nbReleases = count($releases);

                    $mereges = $callapiservice -> GetProjectMergeReq($project['id'], $team -> getGitlabURL(), $team -> getToken());
                    $nbMerges = count($mereges);

                    $commits = $callapiservice -> GetProjectCommits($project['id'], $team -> getGitlabURL(), $team -> getToken());
                    $nbCommits = count($commits);

                     
                  //  $release = $releases[0];
                    if (($notif -> getIdProject() == $project['id']) && ($notif -> getNbReleases() < $nbReleases)){
                        // hedha zidou fel affichage yaatik 9adeh men release tzed bedhabt 
                        $nbActionToAdd = $nbReleases - $notif -> getNbReleases();

                      

                      $update = new Update(
                            '/release',
                            json_encode(['action' => 'release',
                            'project' => $project['id'],
                            // 'newReleaseName' => $release['name'],
                             'nbRelease' => $nbReleases,
                            'server' => $team -> getGitlabURL(),
                            'lat'=> $team -> getMap()-> getLattitude(),
                            'long'=> $team -> getMap()-> getLongitude(),

                            ])
                        );
                   
                       
                        $hub->publish($update);
                        $notif -> setNbReleases($nbReleases);
                        $notification = new Notification();
                        $notification -> setDateNotif(new DateTime('NOW'));
                        $notification -> setContenu('New release in project with id : '
                        .$project['id'].' in team  : '.$team -> getGitlabURL());
                        $notification -> setAction('Release');

                        $em-> persist($notification);
                        $em->flush(); 
                        
                     /*   return $this -> json([
                            'dataRGit' => $nbReleases,
                            'projectId' => $project['id'],
                            'idProjBASESDB' => $notif->getIdProject(),
                            'REALEAEFELBD' => $notif -> getNbReleases()
                        ]);*/
                    }
                    else if (($notif -> getIdProject() == $project['id']) && ($notif -> getNbMerges() < $nbMerges)){
                        // hedha zidou fel affichage yaatik 9adeh men release tzed bedhabt 
                        $nbActionToAdd = $nbMerges - $notif -> getNbMerges();

                      

                      $update = new Update(
                            '/mergeReq',
                            json_encode(['action' => 'merge',
                            'project' => $project['id'],
                            // 'newReleaseName' => $release['name'],
                             'nbMerge' => $nbMerges,
                            'server' => $team -> getGitlabURL(),
                        
                            'lat'=> $team -> getMap()-> getLattitude(),
                            'long'=> $team -> getMap()-> getLongitude(),

                            ])
                        );
                   
                       
                        $hub->publish($update);
                        $notif -> setNbMerges($nbMerges);
                        $notification = new Notification();
                        $notification -> setDateNotif(new DateTime('NOW'));
                        $notification -> setContenu('New merge in project with id : '
                        .$project['id'].' in team  : '.$team -> getGitlabURL());
                        $notification -> setAction('MergeReq');

                        $em-> persist($notification);
                        $em->flush(); 
                        
                     /*   return $this -> json([
                            'dataRGit' => $nbReleases,
                            'projectId' => $project['id'],
                            'idProjBASESDB' => $notif->getIdProject(),
                            'REALEAEFELBD' => $notif -> getNbReleases()
                        ]);*/
                     }
                     else if (($notif -> getIdProject() == $project['id']) && ($notif -> getNbCommits() < $nbCommits)){
                        // hedha zidou fel affichage yaatik 9adeh men release tzed bedhabt 
                        $nbActionToAdd = $nbCommits - $notif -> getNbCommits();

                      

                      $update = new Update(
                            '/commit',
                            json_encode(['action' => 'commit',
                            'project' => $project['id'],
                            // 'newReleaseName' => $release['name'],
                             'nbCommit' => $nbCommits,
                            'server' => $team -> getGitlabURL(),
                        
                            'lat'=> $team -> getMap()-> getLattitude(),
                            'long'=> $team -> getMap()-> getLongitude(),

                            ])
                        );
                   
                       
                        $hub->publish($update);
                        $notif -> setNbCommits($nbCommits);
                        $notification = new Notification();
                        $notification -> setDateNotif(new DateTime('NOW'));
                        $notification -> setContenu('New commit in project with id : '
                        .$project['id'].' in team  : '.$team -> getGitlabURL());
                        $notification -> setAction('commit');

                        $em-> persist($notification);
                        $em->flush(); 
                        
                     /*   return $this -> json([
                            'dataRGit' => $nbReleases,
                            'projectId' => $project['id'],
                            'idProjBASESDB' => $notif->getIdProject(),
                            'REALEAEFELBD' => $notif -> getNbReleases()
                        ]);*/
                     }
            }
            }
        }

        return new Response('publishedToTestACTIONMERCURE!');
    }

}
