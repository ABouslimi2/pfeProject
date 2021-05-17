<?php

namespace App\Controller;

use App\Entity\ServerEndpoint;
use App\Repository\LDSMapRepository;
use App\Repository\MapLDSRepository;
use App\Repository\ServerEndpointRepository;
use App\Service\CallApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/AdminDashboard", name="AdminDashboard")
     */
    public function index(CallApiService $callapiservice, ServerEndpointRepository $sp,MapLDSRepository $mapRep): Response
    {
        $teamsName = [];
        $projects = [];
        $commitNumber = 0 ;
        $em = $this->getDoctrine()->getManager();
        $teams = $em->getRepository('App\Entity\ServerEndpoint')->findAll();
        $aaaa = [];
        $locations =  $mapRep -> findAll();
       // $teams =  $sp -> findTeamByType($type);
       // dd($locations);
        foreach ($teams as $team) {
            foreach ($locations as $loc) {
                   // if ($t -> getMap() != null &&  $loc ->getId() == $t->getMap()->getId()) {
                       if ($loc ->getId() == 6 && $team -> getId() == 27){
                      //  dd('aaaa');
                        $projects= $callapiservice -> GetGitLabProjects($team-> getGitlabURL(), $team -> getToken());
                        foreach ($projects as $proj) {
                            $commitNumber = $commitNumber + count($callapiservice -> GetProjectCommits($proj['id'], $team -> getGitlabURL(), $team -> getToken()));
                        }                   
                         $loc->commits = $commitNumber;

                    
                }
            }
        
        } 
        //dd($teams);
        dd($locations);
        foreach ($teams as $name){
            array_push($teamsName,$name->getTeam());
        }
        return $this->render('admin/dashboard.html.twig', [
            'teams' => $teams,   
            'names' => json_encode($teamsName), 
            'locations' => json_encode($locations), 
        ]);
        
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

    public function getLocation(MapLDSRepository $mapRep): Response
    {
        $locations =  $mapRep -> findAll();
               
        return $this -> json([
            'data' => $locations,
        ], 200);
    }

}
