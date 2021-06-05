<?php

namespace App\Command;

use DateTime;
use App\Entity\Notification;
use App\Service\CallApiService;
use Symfony\Component\Mercure\Update;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use App\Repository\ServerEndpointRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\MercureNotificationsRepository;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckUpdatesCommand extends Command
{
     /**
     *
     * @var EntityManagerInterface
     */
    private $em;

     /**
     *
     * @var MercureNotificationsRepository
     */
    private $mercureNotificationsRep;

     /**
     *
     * @var ServerEndpointRepository
     */
    private $serverEndpointRep;

    public CallApiService $callApiService;
    public HubInterface $hub;
    
    protected static $defaultName = 'app:CheckUpdates';
    //protected static $defaultDescription = 'Add a short description for your command';
    
    public function __construct(EntityManagerInterface $em, MercureNotificationsRepository $mercureNotificationsRep,
    ServerEndpointRepository  $serverEndpointRep, CallApiService $callApiService, HubInterface $hub)
    {
        $this->em = $em;
        $this->mercureNotificationsRep = $mercureNotificationsRep;
        $this -> serverEndpointRep = $serverEndpointRep;
        $this->callApiService = $callApiService;
        $this -> hub= $hub;
        parent::__construct();
    }
    protected function configure()
    {
       
    }

    protected function execute(InputInterface $input, OutputInterface $output):int
    {
       

        $teams = $this->em->getRepository('App\Entity\ServerEndpoint')->findAll();
        $notifs = $this->em->getRepository('App\Entity\MercureNotifications')->findAll();

        foreach($teams as $team){
            $projects = $this-> callApiService -> GetGitLabProjects($team-> getGitlabURL(), $team -> getToken());
            foreach ($projects as $project){
                foreach($notifs as $notif){
                    $releases = $this-> callApiService -> GetProjectReleases($project['id'], $team -> getGitlabURL(), $team -> getToken());
                    $nbReleases = count($releases);

                    $mereges = $this-> callApiService -> GetProjectMergeReq($project['id'], $team -> getGitlabURL(), $team -> getToken());
                    $nbMerges = count($mereges);

                    $commits = $this-> callApiService -> GetProjectCommits($project['id'], $team -> getGitlabURL(), $team -> getToken());
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
                   
                       
                        $this-> hub->publish($update);
                        $notif -> setNbReleases($nbReleases);
                        $notification = new Notification();
                        $notification -> setDateNotif(new DateTime('NOW'));
                        $notification -> setContenu('New release in project with id : '
                        .$project['id'].' in team  : '.$team -> getGitlabURL());
                        $notification -> setAction('Release');

                        $this->em-> persist($notification);
                        $this->em->flush(); 
                        
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
                   
                       
                        $this->hub->publish($update);
                        $notif -> setNbMerges($nbMerges);
                        $notification = new Notification();
                        $notification -> setDateNotif(new DateTime('NOW'));
                        $notification -> setContenu('New merge in project with id : '
                        .$project['id'].' in team  : '.$team -> getGitlabURL());
                        $notification -> setAction('MergeReq');

                        $this->em-> persist($notification);
                        $this->em->flush(); 
                        
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
                   
                       
                        $this->hub->publish($update);
                        $notif -> setNbCommits($nbCommits);
                        $notification = new Notification();
                        $notification -> setDateNotif(new DateTime('NOW'));
                        $notification -> setContenu('New commit in project with id : '
                        .$project['id'].' in team  : '.$team -> getGitlabURL());
                        $notification -> setAction('commit');

                        $this->em-> persist($notification);
                        $this->em->flush(); 
                        
                        
                     /*   return $this -> json([
                            'dataRGit' => $nbReleases,
                            'projectId' => $project['id'],
                            'idProjBASESDB' => $notif->getIdProject(),
                            'REALEAEFELBD' => $notif -> getNbReleases()
                        ]);*/
                     }
                     return Command::SUCCESS;
            }
            }
        }

    }
}
