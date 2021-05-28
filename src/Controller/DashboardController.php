<?php

namespace App\Controller;

use App\Entity\MercureNotifications;
use App\Entity\ServerEndpoint;
use App\Repository\LDSMapRepository;
use App\Repository\MapLDSRepository;
use App\Repository\ServerEndpointRepository;
use App\Service\CallApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\Update;

class DashboardController extends AbstractController
{
    /**
     * @Route("/AdminDashboard", name="AdminDashboard")
     */
    public function index(CallApiService $callapiservice, ServerEndpointRepository $sp,MapLDSRepository $mapRep ,Request $request): Response
    {
        $teamsName = [];
        $teamsURL = [];
        $projects = [];
        $commitNumber = 0 ;
        $releasesNumber = 0 ;
        $em = $this->getDoctrine()->getManager();
        $teams = $em->getRepository('App\Entity\ServerEndpoint')->findAll();
        $locations =  $mapRep -> findAll();
       // $teams =  $sp -> findTeamByType($type);
        foreach ($teams as $team) {
           
            foreach ($locations as $loc) {
                 if ($team -> getMap() == $loc) {
                      
                       //dd('aaaa');
                        $projects= $callapiservice -> GetGitLabProjects($team-> getGitlabURL(), $team -> getToken());
                        foreach ($projects as $proj) {
                            $commitNumber = $commitNumber + count($callapiservice -> GetProjectCommits($proj['id'], $team -> getGitlabURL(), $team -> getToken()));
                            $releasesNumber = $releasesNumber + count($callapiservice -> GetProjectReleases($proj['id'], $team -> getGitlabURL(), $team -> getToken()));

                        }                   
                         $loc->commits = $commitNumber;
                        $loc->releases = $releasesNumber;
                        $releasesNumber = 0 ;
                         $commitNumber = 0 ;
                }
            }
        
        } 
        
        foreach ($teams as $name){
            array_push($teamsName,$name->getTeam());
            array_push($teamsURL,$name ->getGitlabURL());
        }
    
        //$urlServer = array_unique($uniqueServer);
        //dd($urlServer,$teamsName);
        return $this->render('admin/dashboard.html.twig', [
            'teams' => $teams,   
            'names' => json_encode($teamsName), 
            'urls' => json_encode($teamsURL),
            'locations' => json_encode($locations), 
        ]);
        
    }

    //filtre

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
                        }                   
                         $loc->commits = $commitNumber;
                         $loc->releases = $releasesNumber;
                         $releasesNumber = 0 ;
                         $commitNumber = 0 ;
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
    public function invokeMercure(CallApiService $callapiservice, HubInterface $hub): Response
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
                  //  $release = $releases[0];
                    if (($notif -> getIdProject() == $project['id']) && ($notif -> getNbReleases() < $nbReleases)){
                      $update = new Update(
                            '/release',
                            json_encode(['action' => 'release',
                            'project' => $project['id'],
                            'projectName'=>$project['name'],
                            // 'newReleaseName' => $release['name'],
                             'nbRelease' => $nbReleases,
                            'server' => $team -> getGitlabURL(),
                            'team' => $team -> getTeam(),
                            ])
                        );
                
                        $hub->publish($update);
                        $notif -> setNbReleases($nbReleases);
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
