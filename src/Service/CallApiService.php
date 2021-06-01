<?php

namespace App\Service;


use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\request;


class CallApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this-> client = $client;
    }

    public function GetGitLabProjects($url,$token):array 
    {
        if ($url == "https://gitlabtest.linedata.com") {
            $response = $this->client->request(
                'GET',
                $url.'/api/v4/projects?access_token='.$token.'&per_page=100'
            );

            return $response->toArray();
        }
        else {
            $response = $this->client->request(
                'GET',
                $url.'/api/v4/projects?access_token='.$token.'&per_page=100&visibility=private'
            );

            return $response->toArray();
        }
    }

    public function GetGitLabOneProject(int $id, $url, $token):array 
    {
        $response = $this->client->request (
            'GET',
            $url.'/api/v4/projects/'.$id.'?access_token='.$token
        );

      return $response->toArray();
    }

    public function GetProjectMembers(int $id,$url,$token):array 
    {
        $response = $this->client->request (
            'GET',
            $url.'/api/v4/projects/'.$id.'/members?access_token='.$token
        );
        return $response->toArray(); 
    }

    public function GetProjectCommits(int $id,$url,$token):array 
    {
       $response = $this->client->request (
        'GET',
        $url.'/api/v4/projects/'.$id.'/repository/commits?access_token='.$token.'&per_page=1000'
    );

  return $response->toArray();
 
    }

    public function GetProjectBranches(int $id,$url,$token):array 
    {
        $response = $this->client->request (
            'GET',
            $url.'/api/v4/projects/'.$id.'/repository/branches?access_token='.$token.'&per_page=1000'
        );
    
      return $response->toArray();
    }

    public function GetProjectIssues(int $id,$url,$token):array
    {
       
        $response = $this->client->request (
            'GET',
            $url.'/api/v4/projects/'.$id.'/issues?access_token='.$token.'&per_page=1000'
        );
    
      return $response->toArray();
    }

    public function GetProjectTags(int $id,$url,$token):array
    {
        $response = $this->client->request (
            'GET',
            $url.'/api/v4/projects/'.$id.'/repository/tags?access_token='.$token.'&per_page=1000'
        );
    
      return $response->toArray();
    }

    public function GetProjectMergeReq(int $id,$url,$token):array
    {
        $response = $this->client->request (
            'GET',
            $url.'/api/v4/projects/'.$id.'/merge_requests?access_token='.$token
        );
    
      return $response->toArray();
    }

    public function GetProjectReleases(int $id,$url,$token):array
    {
        $response = $this->client->request (
            'GET',
            $url.'/api/v4/projects/'.$id.'/releases?access_token='.$token
        );
    
      return $response->toArray();
    }

    public function GetProjectEvents(int $id,$url,$token):array
    {
        $response = $this->client->request (
            'GET',
            $url.'/api/v4/projects/'.$id.'/events?access_token='.$token
        );
    
      return $response->toArray();
    }

    public function GetProjectPipelines(int $id,$url,$token):array
    {
        $response = $this->client->request (
            'GET',
            $url.'/api/v4/projects/'.$id.'/pipelines?access_token='.$token
        );
    
      return $response->toArray();
    }
   
    public function GetPipelineJobs(int $id,$url,$token):array
    {
        $response = $this->client->request (
            'GET',
            $url.'/api/v4/projects/'.$id.'/jobs?access_token='.$token
        );
    
      return $response->toArray();

    }
/*
    private function getApi(string $var)
    {
        $response = $this->client->request (
            'GET',
            'https://gitlabtest.linedata.com/api/v4/projects/'.$var
        );

        return $response->toArray();
    }*/

    private function postApi(string $var)
    {
        $response = $this->client->request (
            'POST',
            'https://gitlabtest.linedata.com/api/v4/projects/'.$var
        );

        return $response->toArray();
    }

    public function retryPipeline(int $id,int $idpipe,$url,$token)
    {
        $response = $this->client->request (
            'POST',
            $url.'/api/v4/projects/'.$id.'/pipelines/'.$idpipe.'/retry?access_token='.$token
        );
    
      return $response->toArray();
      
    }
    public function cancelPipeline(int $id,int $idpipe,$url,$token)
    {
        $response = $this->client->request (
            'POST',
            $url.'/api/v4/projects/'.$id.'/pipelines/'.$idpipe.'/cancel?access_token='.$token
        );
    
      return $response->toArray();
      
    }
    public function playJob(int $id,int $idjob,$url,$token)
    {
        $response = $this->client->request (
            'POST',
            $url.'/api/v4/projects/'.$id.'/jobs/'.$idjob.'/play?access_token='.$token
        );
    
      return $response->toArray();
      
    }
    public function retryJob(int $id,int $idjob,$url,$token)
    {
        $response = $this->client->request (
            'POST',
            $url.'/api/v4/projects/'.$id.'/jobs/'.$idjob.'/retry?access_token='.$token
        );
    
      return $response->toArray();
      
    }
    public function RunTestPipelineJobs(int $id,$branche,$url,$token)
    {
         $response = $this->client->request (
            'POST',
            $url.'/api/v4/projects/'.$id.'/pipeline?ref='.$branche.'&access_token='.$token
        );
    
      return $response->toArray();
    }
    public function RunTestPipelineJobsWithVars(int $id,$branche,$key,$value,$url,$token)
    {
       // $this->getApiTesttt($id.'/pipeline?ref='.$branche.'&['.$key.']='.$value.'&access_token=fjzuHYWnzGcaEy7PAGxi');
        $response = $this->client->request (
            'POST',
            $url.'/api/v4/projects/'.$id.'/pipeline?ref='.$branche.'&['.$key.']='.$value.'&access_token='.$token
        );
    
      return $response->toArray();
    }
    public function GetGitLabProjectVariables(int $id,$url,$token):array 
    {
        $response = $this->client->request (
            'GET',
            $url.'/api/v4/projects/'.$id.'/variables?access_token='.$token
        );
        return $response->toArray();
    }

    public function postBranch(int $id,$branche,$newBranchName,$url,$token)
    {
         $response = $this->client->request (
            'POST',
            $url.'/api/v4/projects/'.$id.'/repository/branches?ref='.$branche.'&branch='.$newBranchName.'&access_token='.$token
        );
    
      return $response->toArray();
    }

    //*************************************Testtttt****************************************** */
   private function getApiTest(string $var)
   {
    $response = $this->client->request (
        'GET',
        'https://gitlab.com/api/v4/projects/'.$var
    );

    return $response->toArray();
   }

    public function GetGitLabProjectTest(int $id):array 
    {
        return $this->getApiTest($id.'?access_token=fjzuHYWnzGcaEy7PAGxi');
      
    }
    public function GetGitLabProjectTestMembers(int $id):array 
    {
        return $this->getApiTest($id.'/members?access_token=fjzuHYWnzGcaEy7PAGxi');
      
    }
    
    public function GetGitLabProjectTestCommits(int $id):array 
    {
        return $this->getApiTest($id.'/repository/commits?access_token=fjzuHYWnzGcaEy7PAGxi');
      
    }
    public function GetGitLabProjectTestBranches(int $id):array 
    {
        return $this->getApiTest($id.'/repository/branches?access_token=fjzuHYWnzGcaEy7PAGxi');
      
    }
    public function GetGitLabProjectTestIssues(int $id):array 
    {
        return $this->getApiTest($id.'/issues?access_token=fjzuHYWnzGcaEy7PAGxi');
      
    }
    public function GetGitLabProjectTestTags(int $id):array 
    {
        return $this->getApiTest($id.'/repository/tags?access_token=fjzuHYWnzGcaEy7PAGxi');
      
    }
    public function GetGitLabProjectTestMergeReq(int $id):array 
    {
        return $this->getApiTest($id.'/merge_requests?access_token=fjzuHYWnzGcaEy7PAGxi');
      
    }
    public function GetGitLabProjectTestPipelines(int $id):array 
    {
        return $this->getApiTest($id.'/pipelines?access_token=fjzuHYWnzGcaEy7PAGxi&per_page=100');
      
    }
    public function GetGitLabProjectTestPipelinesJobs(int $id):array 
    {
        return $this->getApiTest($id.'/jobs?access_token=fjzuHYWnzGcaEy7PAGxi&per_page=100');
      
    }

    private function getApiTesttt(string $var)
    {
     
     $this->client->request (
         'POST',
         'https://gitlab.com/api/v4/projects/'.$var
     );
    }
    public function GetGitLabProjectTestVariables(int $id):array 
    {
        return $this->getApiTest($id.'/variables?access_token=fjzuHYWnzGcaEy7PAGxi');
      
    }
    
 

   
    
   
}