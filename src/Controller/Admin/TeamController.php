<?php

namespace App\Controller\Admin;

use App\Form\TeamType;
use App\Entity\ServerEndpoint;
use App\Repository\MapLDSRepository;
use App\Repository\ServerEndpointRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeamController extends AbstractController
{
    public function addTeam(ServerEndpointRepository $serverRep,Request $request):Response
    {
      
        $team = new ServerEndpoint();
        if ($request->getMethod() == 'POST') {
            $s1 = $request->get('team');
            $s2 = $request->get('gitlabURL');
            $s3 = $request->get('token');
            $s4 = $request -> get('gitType');
           // $s5 = $request ->get('gitLoc');
           // var_dump($s5);exit();
            $em = $this->getDoctrine()->getManager();
           // $local = $em -> getRepository('App\Entity\MapLDS')->find($s5);
            $team -> setTeam($s1);
            $team -> setGitlabURL($s2);
            $team -> setToken($s3);
            $team -> setGitType($s4);
            $s5 = $request ->get('gitLoc');
            
            $em = $this->getDoctrine()->getManager();
            $local = $em -> getRepository('App\Entity\MapLDS')->find($s5);
            $team -> setTeam($s1);
            $team -> setGitlabURL($s2);
            $team -> setToken($s3);
            $team -> setGitType($s4);
            $team -> setMap($local);
            $emm = $this->getDoctrine()->getManager();
                     // var_dump($s5,$local);exit();
         
            $emm->persist($team);
            $emm->flush();
           // $teams =  $serverRep -> findAll();
            return $this -> json([
               // 'teams' => $teams,
            ], 200);  
    
    
        }
               
     
    }
   
    public function ShowTeams(ServerEndpointRepository $serverRep,Request $request,MapLDSRepository $mapRep):Response
    {
        $teamsName = [];
        $teamsURL = [];
        
        $teamss =  $serverRep -> findAll();
        foreach ($teamss as $name){
            array_push($teamsName,$name->getTeam());
            array_push($teamsURL,$name ->getGitlabURL());
        }
        $teams = $serverRep -> findTeamByTypeGitlab();
        $locations =  $mapRep -> findAll();
        return $this->render("admin/showTeams.html.twig", [
            'teams' => $teams,   
            'names' => json_encode($teamsName), 
            'urls' => json_encode($teamsURL),
            'locationss' => $locations, 
        ]);
    }

     
    public function deleteTeam(int $idTeam,ServerEndpointRepository $serverRep):Response
    {
        
            $em = $this->getDoctrine()->getManager();
            $team = $em->getRepository('App\Entity\ServerEndpoint')->find($idTeam);
            $em -> remove($team);
            $em -> flush();
            
            $teams =  $serverRep -> findAll();
            return $this -> json([
                'teams' => $teams
            ], 200);
        }

    public function editTeam(int $idTeam,ServerEndpointRepository $serverRep,Request $request):Response
    {
            $em = $this->getDoctrine()->getManager();
            $team = $em->getRepository('App\Entity\ServerEndpoint')->find($idTeam);
            if ($request->getMethod() == 'POST') {
                $s1 = $request->get('teamEdit');
                $s2 = $request->get('gitlabURLEdit');
                $s3 = $request->get('tokenEdit');
                $team -> setTeam($s1);
                $team -> setGitlabURL($s2);
                $team -> setToken($s3);
                $em->flush(); 
                $teams =  $serverRep -> findAll();
                return $this -> json([
                    'teams' => $teams
                ], 200); 
                 
            }
            $teams =  $serverRep -> findAll();
            return $this -> json([
                'teams' => $teams
            ], 200);
           
        }

}
