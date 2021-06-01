<?php

namespace App\Command;

use App\Service\CallApiService;
use App\Entity\MercureNotifications;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ServerEndpointRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\MercureNotificationsRepository;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateDataBaseCommand extends Command
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
    
    
    protected static $defaultName = 'app:UpdateDataBase';
    //protected static $defaultDescription = 'Add a short description for your command';
    
    public function __construct(EntityManagerInterface $em, MercureNotificationsRepository $mercureNotificationsRep,
    ServerEndpointRepository  $serverEndpointRep, CallApiService $callApiService)
    {
        $this->em = $em;
        $this->mercureNotificationsRep = $mercureNotificationsRep;
        $this -> serverEndpointRep = $serverEndpointRep;
        $this->callApiService = $callApiService;
        
        parent::__construct();
    }
    protected function configure()
    {
       
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $notif = new MercureNotifications();
        
        $teams = $this->em->getRepository('App\Entity\ServerEndpoint')->findAll();

        foreach ($teams as $team){
            $projects = $this->callapiservice -> GetGitLabProjects($team-> getGitlabURL(), $team -> getToken());
            foreach ($projects as $project){
                $notif = new MercureNotifications();
                $notif -> setIdProject($project['id']);
                $notif -> setNbCommits(count($this->callapiservice -> GetProjectCommits($project['id'], $team -> getGitlabURL(), $team -> getToken())));
                $notif -> setNbMerges(count ($this->callapiservice -> GetProjectMergeReq($project['id'], $team -> getGitlabURL(), $team -> getToken())));
                $notif -> setNbReleases(count($this->callapiservice -> GetProjectReleases($project['id'], $team -> getGitlabURL(), $team -> getToken())));
                $notif -> setNbJobs(count($this->callapiservice -> GetPipelineJobs($project['id'], $team -> getGitlabURL(), $team -> getToken())));
                $notif -> setNbPipes(count($this->callapiservice -> GetProjectPipelines($project['id'], $team -> getGitlabURL(), $team -> getToken())));
                $notif -> setNbIssues(count($this->callapiservice -> GetProjectIssues($project['id'], $team -> getGitlabURL(), $team -> getToken())));
                $this->em->persist($notif);
                $this->em->flush();
            }
        }

        return Command::SUCCESS;
    }
}
