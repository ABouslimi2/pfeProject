<?php

namespace App\Command;

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
            $projects =$this->callApiService-> GetGitLabProjects($team-> getGitlabURL(), $team -> getToken());
            foreach ($projects as $project){
                foreach($notifs as $notif){
                    $releases = $this->callApiService -> GetProjectReleases($project['id'], $team -> getGitlabURL(), $team -> getToken());
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
                
                        $this->hub->publish($update);
                        $notif -> setNbReleases($nbReleases);
                        $this->em->flush(); 
                        // return new Response('publishedToTestACTIONMERCURE!');
                        return Command::SUCCESS;
                    }
                }
            }
        }

    }
}
