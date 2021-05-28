<?php

namespace App\Service;


use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\request;


class ServiceGitHub
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this-> client = $client;
    }

    public function GetGitHubRepos($url,$token):array
    {
        $response = $this->client->request(
            'GET',
            $url.'/user/repos?access_token='.$token.'&per_page=100'
        );

        return $response->toArray();
    }

    public function GetGitHubOneRepo($owner,$name,$url,$token):array
    {
        $response = $this->client->request(
            'GET',
            $url.'/repos/'.$owner.'/'.$name.'?access_token='.$token.'&per_page=100'
        );
        

        return $response->toArray();
    }

    public function GetGitHubCommits($owner,$name,$url,$token):array
    {
        $response = $this->client->request(
            'GET',
            $url.'/repos/'.$owner.'/'.$name.'/commits?access_token='.$token.'&per_page=100'
        );
        

        return $response->toArray();
    }

    public function GetGitHubMembers($owner,$name,$url,$token):array
    {
        $response = $this->client->request(
            'GET',
            $url.'/repos/'.$owner.'/'.$name.'/collaborators?access_token='.$token.'&per_page=100'
        );
        

        return $response->toArray();
    }
    public function GetGitHubBranches($owner,$name,$url,$token):array
    {
        $response = $this->client->request(
            'GET',
            $url.'/repos/'.$owner.'/'.$name.'/branches?access_token='.$token.'&per_page=100'
        );
        

        return $response->toArray();
    }
    public function GetGitHubIssues($owner,$name,$url,$token):array
    {
        $response = $this->client->request(
            'GET',
            $url.'/repos/'.$owner.'/'.$name.'/issues?access_token='.$token.'&per_page=100'
        );
        

        return $response->toArray();
    }

}