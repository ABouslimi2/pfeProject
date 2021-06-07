<?php

namespace App\Controller;

use App\Repository\ServerEndpointRepository;
use App\Service\ServiceGitHub;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GitHubController extends AbstractController
{
    
    public function index(ServiceGitHub $serviceGitHub,$idTeam,ServerEndpointRepository $sp)
    {
        $team = $sp ->find($idTeam);
        $url = $team -> getGitlabURL();
        $token = $team -> getToken();

        return $this->render('git_hub/index.html.twig', [
            'data' => $serviceGitHub -> GetGitHubRepos($url,$token),
            'team' => $team,
        ]);
    }

    public function showDetails(ServiceGitHub $serviceGitHub,$idTeam,ServerEndpointRepository $sp,$owner,$name)
    {
        $team = $sp ->find($idTeam);
        $url = $team -> getGitlabURL();
        $token = $team -> getToken();

        $commits= $serviceGitHub -> GetGitHubCommits($owner,$name,$url,$token);
        $commitNb= count($commits);
        $members= $serviceGitHub -> GetGitHubMembers($owner,$name,$url,$token);
        $number=count($members);
        $branches= $serviceGitHub -> GetGitHubBranches($owner,$name,$url,$token);
        $brancheNB= count($branches);
        $issues= $serviceGitHub -> GetGitHubIssues($owner,$name,$url,$token);
        $issuesNB=count($issues);
        $releases= $serviceGitHub -> GetGitHubReleases($owner,$name,$url,$token);
        $releasesNB= count($releases);

        return $this->render('git_hub/detailsRepos.html.twig', [
            'data' => $serviceGitHub -> GetGitHubOneRepo($owner,$name,$url,$token),
            'team' => $team,
            'commits' => $commits,
            'commitNb' => $commitNb,
            'members' => $members,
            'number' => $number,
            'branches' => $branches,
            'brancheNB' => $brancheNB,
            'issues' => $issues,
            'issuesNB' => $issuesNB,
            'releases' => $releases,
            'releasesNB' => $releasesNB
        ]);
    }

}
