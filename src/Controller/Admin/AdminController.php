<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ServerEndpoint;
use App\Repository\ServerEndpointRepository;
use App\Service\CallApiService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @Route("/admin", name="apiListGitLab")
*/
class AdminController extends AbstractController
{
    

    //Fonction de tri date
    function sortFunction( $a, $b ) {
        return strtotime($a["date"]) - strtotime($b["date"]);
    }
    //Fonction pour comparer les dates
    function compare_date_keys($dt1, $dt2) {
        return strtotime($dt1) - strtotime($dt2);
    }

    //fonction pour trier le tableau commits selon le commiter name
    function cmp($a, $b) {

     $propertyAccessor = PropertyAccess::createPropertyAccessor();

    return strcmp($propertyAccessor->getValue($a,'[committer_name]'), $propertyAccessor->getValue($b,'[committer_name]'));
    }

    public function apiListGitLab(CallApiService $callapiservice,$idTeam, ServerEndpointRepository $sp)
    {
        $team = $sp ->find($idTeam);
        $url = $team -> getGitlabURL();
        $token = $team -> getToken();

        $teamStats = [];
        $projects = [];
        $nbCommit = 0 ;
        $nbMerge = 0 ;
        $nbRelease = 0 ;
        $projectsStats = [];
      
            
            $projects= $callapiservice -> GetGitLabProjects($url, $token);
            
            foreach ($projects as $project){
                $nbCommit =  count ($callapiservice -> GetProjectCommits($project['id'], $team -> getGitlabURL(), $team -> getToken()));
                $nbMerge =  count ($callapiservice -> GetProjectMergeReq($project['id'], $team -> getGitlabURL(), $team -> getToken()));
                $nbRelease =  count ($callapiservice -> GetProjectReleases($project['id'], $team -> getGitlabURL(), $team -> getToken()));
                array_push($projectsStats, array( $project['name'] => array( 'commits' => $nbCommit , 'releases' => $nbRelease , 'merges' => $nbMerge)));
            }
      





        return $this->render("admin/home.html.twig", [
            'data' => $callapiservice -> GetGitLabProjects($url, $token),
            'team' => $team,
            'projectsStats' => json_encode($projectsStats),
        ]);
    }

  
    public function showDetails(int $id,CallApiService $callapiservice,Request $request,
    PaginatorInterface $paginator, $idTeam, ServerEndpointRepository $sp):Response
    {
        //declaration propertyAccess
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        
        $team = $sp ->find($idTeam);
        $url = $team -> getGitlabURL();
        $token = $team -> getToken();
        $project = $callapiservice -> GetGitLabOneProject($id, $url, $token);
        $members=$callapiservice -> GetProjectMembers($id,$url,$token);
        $commits=$callapiservice -> GetProjectCommits($id,$url,$token);
        $branches=$callapiservice -> GetProjectBranches($id,$url,$token);
        $issues=$callapiservice -> GetProjectIssues($id,$url,$token);
        $tags=$callapiservice ->GetProjectTags($id,$url,$token);
        $mergeReq=$callapiservice ->GetProjectMergeReq($id,$url,$token);
        $releases=$callapiservice -> GetProjectReleases($id,$url,$token);
        $events=$callapiservice -> GetProjectEvents($id,$url,$token);
        $number=count($members);
        $commitNb=count($commits);
        $brancheNB=count($branches);
        $issuesNB=count($issues);
        $tagNB=count($tags);
        $MergeReqNB=count($mergeReq);
        $releaseNB=count($releases);
        $eventNB=count($events);
        $pipelines=$callapiservice -> GetProjectPipelines($id,$url,$token);
        $idspipeline = [];

        $nameBranche = array();
        foreach ($branches as $b) {
            array_push($nameBranche, $b['name']);
        }
        



        foreach($pipelines as $pipeline){ 
            //array_push(nom_tableau , haja li theb tpousheha)
            // property  accessor c'est pour acceder l variable fi objet
            array_push($idspipeline, (int) $propertyAccessor->getValue( $pipeline, '[id]'));
        }
        
        $labelsDate = [];
        $labelssDate = [];
        $labels = [];
        $data = [];
        $labelsDateStringMerge = [];
        $labelsDateDateMerge = [];

        //trier tab commits
        usort($commits, array("App\Controller\Admin\AdminController", "cmp"));

        foreach($commits as $commit){ 
            //array_push(nom_tableau , haja li theb tpousheha)
            // property  accessor c'est pour acceder l variable fi objet
            array_push($labels, $propertyAccessor->getValue($commit, '[author_name]'));
        }

        //labelsDate contient la liste des dates sous format String
        foreach($commits as $commit){
            array_push($labelsDate,  $propertyAccessor->getValue($commit, '[created_at]'));
            
         }

          //Parcourir Liste des dates sous format String
        foreach ($labelsDate as $dd){ 
            //String ==> date 
            $date = strtotime($dd);
            
            //DateTime ==> date 
            $newformat = date('Y-m-d',$date);
            //labelSSDate contient la liste des dates sous format Date
            array_push($labelssDate, $newformat);
           
        }

        foreach($mergeReq as $merge){
            array_push($labelsDateStringMerge,  $propertyAccessor->getValue($merge, '[created_at]'));
            
         }

          //Parcourir Liste des dates sous format String
        foreach ($labelsDateStringMerge as $dd){ 
            //String ==> date 
            $date = strtotime($dd);
            
            //DateTime ==> date 
            $newformat = date('Y-m-d',$date);
            //labelSSDate contient la liste des dates sous format Date
            array_push($labelsDateDateMerge, $newformat);
           
        }

        //Tri date
        //usort (tableau , fonction pour trier)
        usort($labelssDate, array("App\Controller\Admin\AdminController", "compare_date_keys"));
        usort($labelsDateDateMerge, array("App\Controller\Admin\AdminController", "compare_date_keys"));

        //nombre occurence dans un tableau
        $data = array_count_values($labels);
        $dataDate = array_count_values($labelssDate);
        $dataMerge= array_count_values($labelsDateDateMerge);
// var_dump($projectsStats);exit();
        $commit =  $callapiservice -> GetProjectCommits($id,$url,$token);
        $commits = $paginator->paginate(
            $commit, /* query NOT result */
            $request->query->getInt('otherPage', 1)/*page number*/,
            10,/*limit per page*/
            ['pageParameterName' => 'otherPage']
        );
        
        return $this->render("admin/details.html.twig", [
            'data' => $project,
            'number' => $number,
            'commitNb' => $commitNb,
            'brancheNB' => $brancheNB,
            'issuesNB' => $issuesNB,
            'tagNb' => $tagNB,
            'mergeReqNb' => $MergeReqNB,
            'releaseNB' => $releaseNB,
            'eventNb' => $eventNB,
            'members' => $members,
            'branches' => $branches,
            'commits' => $commits,
            'merges' => $mergeReq,
            'issues' => $issues,
            'releases' => $releases,
            'tags'=> $tags,
            'dataa' => json_encode($data),
            'dataDate' => json_encode($dataDate),
            'team' => $team ,
            
            'idTeam' => $team->getId(),
            'nameBranche' => json_encode($nameBranche),
            
        ]);
    }

   
    
    public function showPipelines(int $id,CallApiService $callapiservice,Request $request,
    PaginatorInterface $paginator,$idTeam, ServerEndpointRepository $sp):Response
    {
        $team = $sp ->find($idTeam);
        $url = $team -> getGitlabURL();
        $token = $team -> getToken();
        $pipes = $callapiservice -> GetProjectPipelines($id,$url,$token);

        $pipess = $paginator->paginate(
            $pipes, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render("admin/pipelines.html.twig", [
            'data' => $callapiservice -> GetGitLabOneProject($id,$url,$token),
            'pipelines' => $callapiservice -> GetProjectPipelines($id,$url,$token),
            'jobs' => $callapiservice -> GetPipelineJobs($id,$url,$token),
            'pipess' => $pipess,
            'team' => $team
        ]);
    }

    
    public function retryPipelines(int $id,int $idpipe,CallApiService $callapiservice,Request $request,
    PaginatorInterface $paginator,$idTeam, ServerEndpointRepository $sp):Response
    
    {
        $team = $sp ->find($idTeam);
        $url = $team -> getGitlabURL();
        $token = $team -> getToken();
         $callapiservice -> retryPipeline($id,$idpipe,$url,$token);
         $pipes = $callapiservice -> GetProjectPipelines($id,$url,$token);

         $pipess = $paginator->paginate(
             $pipes, /* query NOT result */
             $request->query->getInt('page', 1)/*page number*/,
             10/*limit per page*/
         );
        return $this->render("admin/pipelines.html.twig", [
            'data' => $callapiservice -> GetGitLabOneProject($id,$url,$token),

            'pipelines' => $callapiservice -> GetProjectPipelines($id,$url,$token),
            'jobs' => $callapiservice -> GetPipelineJobs($id,$url,$token),
            'pipess' => $pipess,
            'team' => $team
        ]);
    }

   
    public function playJob (int $id,int $idjob,CallApiService $callapiservice,Request $request,
    PaginatorInterface $paginator,$idTeam, ServerEndpointRepository $sp):Response
    
    {
        $team = $sp ->find($idTeam);
        $url = $team -> getGitlabURL();
        $token = $team -> getToken();
         $callapiservice -> playJob($id,$idjob,$url,$token);
         $pipes = $callapiservice -> GetProjectPipelines($id,$url,$token);

         $pipess = $paginator->paginate(
             $pipes, /* query NOT result */
             $request->query->getInt('page', 1)/*page number*/,
             10/*limit per page*/
         );
       //  return $this-> redirectToRoute(())
        return $this->render("admin/pipelines.html.twig", [
            'data' => $callapiservice -> GetGitLabOneProject($id,$url,$token),

            'pipelines' => $callapiservice -> GetProjectPipelines($id,$url,$token),
            'jobs' => $callapiservice -> GetPipelineJobs($id,$url,$token),
            'pipess' => $pipess,
            'team' => $team
        ]);
    }

   
    public function retryJob (int $id,int $idjob,CallApiService $callapiservice,Request $request,
    PaginatorInterface $paginator,$idTeam, ServerEndpointRepository $sp):Response
    
    {
        $team = $sp ->find($idTeam);
        $url = $team -> getGitlabURL();
        $token = $team -> getToken();
         $callapiservice -> retryJob($id,$idjob,$url,$token);
         $pipes = $callapiservice -> GetProjectPipelines($id,$url,$token);

         $pipess = $paginator->paginate(
             $pipes, /* query NOT result */
             $request->query->getInt('page', 1)/*page number*/,
             10/*limit per page*/
         );
        return $this->render("admin/pipelines.html.twig", [
            'data' => $callapiservice -> GetGitLabOneProject($id,$url,$token),

            'pipelines' => $callapiservice -> GetProjectPipelines($id,$url,$token),
            'jobs' => $callapiservice -> GetPipelineJobs($id,$url,$token),
            'pipess' => $pipess,
            'team' => $team
        ]);
    }

   
    public function cancelPipeline (int $id,int $idpipe,CallApiService $callapiservice,Request $request,
    PaginatorInterface $paginator,$idTeam, ServerEndpointRepository $sp):Response
    
    {
        $team = $sp ->find($idTeam);
        $url = $team -> getGitlabURL();
        $token = $team -> getToken();
         $callapiservice -> cancelPipeline($id,$idpipe,$url,$token);
         $pipes = $callapiservice -> GetProjectPipelines($id,$url,$token);

         $pipess = $paginator->paginate(
             $pipes, /* query NOT result */
             $request->query->getInt('page', 1)/*page number*/,
             10/*limit per page*/
         );
        return $this->render("admin/pipelines.html.twig", [
            'data' => $callapiservice -> GetGitLabOneProject($id,$url,$token),

            'pipelines' => $callapiservice -> GetProjectPipelines($id,$url,$token),
            'jobs' => $callapiservice -> GetPipelineJobs($id,$url,$token),
            'pipess' => $pipess,
            'team' => $team
        ]);
    }


  

    public function RunPipeline(int $id, CallApiService $callapiservice, Request $request,$idTeam, ServerEndpointRepository $sp): Response
    {
        $team = $sp ->find($idTeam);
        $url = $team -> getGitlabURL();
        $token = $team -> getToken();
        $branches=$callapiservice -> GetProjectBranches($id,$url,$token);
        
        $variables = $callapiservice-> GetGitLabProjectVariables($id,$url,$token);

        $nameBranche = array();
        foreach ($branches as $b) {
            array_push($nameBranche, $b['name']);
        }
        

        if ($request->getMethod() == 'POST') {
            $dataBranch = $request->get('inputBranch');
            $dataVariables = $request->get('inputVariable');
            $dataVariablesQA = $request->get('inputVariableQA');

            // run pipe sans variables
            if ($dataVariables == 'Choose...') {
                $callapiservice->RunTestPipelineJobs($id, $dataBranch,$url,$token);
                return $this->redirectToRoute('show_pipelines', [
                    'id' => $id,
                    'idTeam' => $team->getId(),
                    'team' => $team
                ]);
            }
            //run with variables
            else {
                if ($team -> getTeam() == "Digital Factory"){
                    $callapiservice->RunTestPipelineJobsWithVars($id, $dataBranch, 'QA_INSTANCE', $dataVariablesQA, $url, $token);
                    return $this->redirectToRoute('show_pipelines', [
                        'id' => $id,
                        'idTeam' => $team->getId(),
                        'team' => $team
                    ]);
                }
                else {
                    
                    foreach ($variables as $var) {
                        // ken l key mtaa l variable li nahna tawa feha fel loop == l key li dakhalneh f select
                        // nlanciw l pipe
                        if ($var['key'] == $dataVariables) {
                            $callapiservice->RunTestPipelineJobsWithVars($id, $dataBranch, $var['key'], $var['value'],$url,$token);
                            return $this->redirectToRoute('show_pipelines', [
                            'id' => $id,
                            'idTeam' => $team->getId(),
                            'team' => $team
                        ]);
               
                    }
                    }
                }
            }
        }
        return $this->render("admin/runPipeline.html.twig", [
            'data' => $callapiservice -> GetGitLabOneProject($id, $url, $token),
            'idTeam' => $team->getId(),
            'nameBranche' => $nameBranche,
            'branches' => $branches,
            'id' => $id,
            'variables' => $variables,
            'team' => $team
        ]);
    }

    public function RunPipelineNoVars(int $id, CallApiService $callapiservice, Request $request,$idTeam, ServerEndpointRepository $sp): Response
    {
        $team = $sp ->find($idTeam);
        $url = $team -> getGitlabURL();
        $token = $team -> getToken();
        $branches=$callapiservice -> GetProjectBranches($id,$url,$token);
        

        $nameBranche = array();
        foreach ($branches as $b) {
            array_push($nameBranche, $b['name']);
        }
        

        if ($request->getMethod() == 'POST') {
            $dataBranch1 = $request->get('inputBranch1');
            

            // run pipe sans variables
            
                $callapiservice->RunTestPipelineJobs($id, $dataBranch1,$url,$token);
                return $this->redirectToRoute('show_pipelines', [
                    'id' => $id,
                    'idTeam' => $team->getId(),
                    'team' => $team
                ]);
            
            //run with variables
          
        }
        return $this->render("admin/runPipeline.html.twig", [
            'data' => $callapiservice -> GetGitLabOneProject($id, $url, $token),
            'idTeam' => $team->getId(),
            'nameBranche' => $nameBranche,
            'branches' => $branches,
            'id' => $id,
            'team' => $team
        ]);
    }

    public function addBranch(int $id, CallApiService $callapiservice, Request $request,$idTeam, ServerEndpointRepository $sp): Response 
    {        
         $team = $sp ->find($idTeam);     
             $url = $team -> getGitlabURL();    
                  $token = $team -> getToken();   
                       // $branches=$callapiservice -> GetProjectBranches($id,$url,$token);   
                                     if ($request->getMethod() == 'POST') {     
                                        $createdFromB = $request->get('inputVariableB');          
                                           $newBranchName = $request->get('newB');      
                                                   $callapiservice->postBranch($id,$createdFromB,$newBranchName,$url,$token);  
                                                               return $this -> json([     
                                                                  ], 200);                 
                                        }        
                                         return $this -> json([       
                                              ], 200);                  }

  

}
