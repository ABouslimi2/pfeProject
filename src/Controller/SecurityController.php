<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    
    /**
     * @Route("/loginCustomm", name="loginCustomm")
     */
    public function loginCustomm(): Response
    {
        return $this->render('login/test.html.twig');

    }
    /**
     * @Route("/loginCustom", name="loginCustom")
     */
    public function loginCustom(Request $request): Response
    {
        if ($request->getMethod() == 'POST') {
            $user = $request->get('user');
            $password = $request->get('password');

            global $messages;
            global $message_css;
        
            $ldap_host = "ln1ads01.ad.linedata.com";
        
            // active directory DN (base location of ldap search)
            $ldap_dn = 'OU=Regional,DC=ad,DC=linedata,DC=com';
        
            // domain, for purposes of constructing $user
            $ldap_usr_dom = $user."@ad.linedata.com";
            $port="636";
            //display error
            error_reporting(0);
            // connect to active directory
            $ldap = ldap_connect($ldap_host);
        
            
        
            // configure ldap params
            ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
        
            //serach
            //$attr = array("memberof");
            //$filter="(sAMAccountName=".$user.")";
            $bind = @ldap_bind($ldap, $ldap_usr_dom, $password);
            //var_dump($bind);die;
            // existing usename
           
        
            if ($bind) {
                // valid
                // check presence in groups
                $filter="(&(objectCategory=person)(objectClass=user)(sAMAccountName=".$user."))";
                //$filter="(&(objectCategory=person)(objectClass=user)(sAMAccountName=".$user."))";
                $justthese = array("ou", "sn", "givenname", "mail","badpwdcount","lastlogon","employeeid","sAMAccountName","cn");
                $result = ldap_search($ldap, $ldap_dn, $filter, $justthese) or exit("Unable to search LDAP server");
                $info = ldap_get_entries($ldap, $result);
        
                //var_dump($info);die;
                $passwordRetryCount=$info[0]['badpwdcount'][0];
                //var_dump($info);
                //;
        
                //if($info[0]['badpwdcount'][0]==0){
                //  var_dump($info[0]['badpwdcount']);die('test');
                //}
                if ($passwordRetryCount == 3) {
                    $messages[] = "Username or Password Incorrect - Login Failed.";
                    return false;
                }
        
                //var_dump( $info[0]['employeeid'][0]);die;
                // $con = DBConnection::getInstance(new DSN("kenuser"));
                // $con->openConnection();
                // $session = new Security_Session();
                // $session->start($info[0]['employeeid'][0]);
                // var_dump($session->start($info[0]['employeeid'][0]));die;
                //header("Location: /");
                //var_dump($info[0]['employeeid'][0]);die;
                ldap_unbind($ldap);
                return $this -> json([
                    'info' => $info,
                ], 200);
            }
           
        }
        return $this -> json([
            'test' => 'test',
        ], 200);
    }
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        return $this->redirect($this->generateUrl('login'));
    }
}
