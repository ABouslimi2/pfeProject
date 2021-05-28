<?php

namespace App\Controller\Admin;

use App\Entity\ServerEndpoint;
use App\Form\TeamType;
use App\Repository\ServerEndpointRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

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
   
    public function ShowTeams(ServerEndpointRepository $serverRep,Request $request):Response
    {
        $teamsName = [];
        $teamsURL = [];
        
        $teamss =  $serverRep -> findAll();
        foreach ($teamss as $name){
            array_push($teamsName,$name->getTeam());
            array_push($teamsURL,$name ->getGitlabURL());
        }
        $teams = $serverRep -> findTeamByTypeGitlab();
        return $this->render("admin/showTeams.html.twig", [
            'teams' => $teams,   
            'names' => json_encode($teamsName), 
            'urls' => json_encode($teamsURL), 
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
