<?php

namespace App\Controller;

use App\Service\CallApiService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeTestController extends AbstractController
{


    /**
     * @Route("/home/test/pipeline/{id}", name="TestPipeline")
     */
    public function RunPipeline(int $id, CallApiService $callapiservice, Request $request): Response
    {
        $branches = $callapiservice->GetGitLabProjectTestBranches($id);
        $nameBranche = array();
        foreach ($branches as $b) {
            array_push($nameBranche, $b['name']);
        }
        $variables = $callapiservice->GetGitLabProjectTestVariables($id);

        if ($request->getMethod() == 'POST') {
            $dataBranch = $request->get('inputBranch');
            $dataVariables = $request->get('inputVariable');

            // run pipe sans variables
            if ($dataVariables == 'Choose...') {
                $callapiservice->RunTestPipelineJobs($id, $dataBranch);
                return $this->redirectToRoute('Testdetails', [
                    'id' => $id
                ]);
            }
            //run with variables
            else {
                foreach ($variables as $var) {
                    // ken l key mtaa l variable li nahna tawa feha fel loop == l key li dakhalneh f select
                    // nlanciw l pipe
                    if ($var['key'] == $dataVariables) {
                        $callapiservice->RunTestPipelineJobsWithVars($id, $dataBranch, $var['key'], $var['value']);
                        return $this->redirectToRoute('Testdetails', [
                            'id' => $id
                        ]);
                    }
                }
            }
        }
        return $this->render("home_test/pipeline.html.twig", [
            'data' => $callapiservice->GetGitLabProjectTest($id),
            'nameBranche' => $nameBranche,
            'branches' => $callapiservice->GetGitLabProjectTestBranches($id),
            'id' => $id,
            'variables' => $variables
        ]);
    }

    //Fonction de tri date
    function sortFunction($a, $b)
    {
        return strtotime($a["date"]) - strtotime($b["date"]);
    }
    //Fonction pour comparer les dates
    function compare_date_keys($dt1, $dt2)
    {
        return strtotime($dt1) - strtotime($dt2);
    }

    //fonction pour trier le tableau commits selon le commiter name
    function cmp($a, $b)
    {
        return strcmp($a['committer_name'],$b['committer_name']);
    }

    /**
     * @Route("/home/test/{id}", name="home_test")
     */
    public function index(CallApiService $callapiservice, int $id): Response
    {
        return $this->render('home_test/index.html.twig', [
            'data' => $callapiservice->GetGitLabProjectTest($id),
        ]);
    }

    /**
     * @Route("/home/test/details/{id}", name="Testdetails")
     */
    public function showDetails(Request $request, int $id, 
    CallApiService $callapiservice, PaginatorInterface $paginator): Response
    {

        $members = $callapiservice->GetGitLabProjectTestMembers($id);
        $commits = $callapiservice->GetGitLabProjectTestCommits($id);
        $branches = $callapiservice->GetGitLabProjectTestBranches($id);
        $issues = $callapiservice->GetGitLabProjectTestIssues($id);
        $tags = $callapiservice->GetGitLabProjectTestTags($id);
        $mergeReq = $callapiservice->GetGitLabProjectTestMergeReq($id);
        $number = count($members);
        $commitNb = count($commits);
        $brancheNB = count($branches);
        $issuesNB = count($issues);
        $tagNB = count($tags);
        $MergeReqNB = count($mergeReq);
        $pipelines = $callapiservice->GetGitLabProjectTestPipelines($id);

        $labelsDate = [];
        $labelssDate = [];
        $labels = [];
        $data = [];
        $labelsDateStringMerge = [];
        $labelsDateDateMerge = [];

        //trier tab commits
        usort($commits, array("App\Controller\HomeTestController", "cmp"));
        //pagination
        $commits = $paginator->paginate(
            $commits, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            2/*limit per page*/
        );

        foreach ($commits as $commit) {
            //array_push(nom_tableau , haja li theb tpousheha)
            // property  accessor c'est pour acceder l variable fi objet
            array_push($labels, $commit['author_name']);
        }

        //labelsDate contient la liste des dates sous format String
        foreach ($commits as $commit) {
            array_push($labelsDate, $commit['created_at']);
        }

        //Parcourir Liste des dates sous format String
        foreach ($labelsDate as $dd) {
            //String ==> date 
            $date = strtotime($dd);

            //DateTime ==> date 
            $newformat = date('Y-m-d', $date);
            //labelSSDate contient la liste des dates sous format Date
            array_push($labelssDate, $newformat);
        }

        foreach ($mergeReq as $merge) {
            array_push($labelsDateStringMerge, $merge['created_at']);
        }

        //Parcourir Liste des dates sous format String
        foreach ($labelsDateStringMerge as $dd) {
            //String ==> date 
            $date = strtotime($dd);

            //DateTime ==> date 
            $newformat = date('Y-m-d', $date);
            //labelSSDate contient la liste des dates sous format Date
            array_push($labelsDateDateMerge, $newformat);
        }

        //Tri date
        //usort (tableau , fonction pour trier)
        usort($labelssDate, array("App\Controller\HomeTestController", "compare_date_keys"));
        usort($labelsDateDateMerge, array("App\Controller\HomeTestController", "compare_date_keys"));

        //nombre occurence dans un tableau
        $data = array_count_values($labels);
        $dataDate = array_count_values($labelssDate);
        $dataMerge = array_count_values($labelsDateDateMerge);

       
        $commit = $callapiservice->GetGitLabProjectTestCommits($id);
        $commits = $paginator->paginate(
            $commit, /* query NOT result */
            $request->query->getInt('otherPage', 1)/*page number*/,
            2,/*limit per page*/
            ['pageParameterName' => 'otherPage']
        );
        return $this->render("home_test/details.html.twig", [
            'data' => $callapiservice->GetGitLabProjectTest($id),
            'number' => $number,
            'commitNb' => $commitNb,
            'brancheNB' => $brancheNB,
            'issuesNB' => $issuesNB,
            'tagNb' => $tagNB,
            'mergeReqNb' => $MergeReqNB,
            'members' => $callapiservice->GetGitLabProjectTestMembers($id),
            'branches' => $callapiservice->GetGitLabProjectTestBranches($id),
            'commits' => $commits,
            'merges' => $callapiservice->GetGitLabProjectTestMergeReq($id),
            'issues' => $callapiservice->GetGitLabProjectTestIssues($id),
            'dataa' => json_encode($data),
            'dataDate' => json_encode($dataDate),
            'dataMerge' => json_encode($dataMerge),
            
            'jobs' => $callapiservice->GetGitLabProjectTestPipelinesJobs($id),
        ]);
    }
    /**
     * @Route(" /home/test/Listpipelines/{id}", name="showTestpipelines")
     */
    public function showTestpipelines(int $id,CallApiService $callapiservice,Request $request,
    PaginatorInterface $paginator):Response
    {
        $pipes = $callapiservice -> GetGitLabProjectTestPipelines($id);

        $pipess = $paginator->paginate(
            $pipes, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render("home_test/listpipelines.html.twig", [
            'data' => $callapiservice->GetGitLabProjectTest($id),

            'pipelines' => $callapiservice ->GetGitLabProjectTestPipelines($id),
            'jobs' => $callapiservice ->GetGitLabProjectTestPipelinesJobs($id),
            'pipess' => $pipess,
        ]);
    }
    
}
