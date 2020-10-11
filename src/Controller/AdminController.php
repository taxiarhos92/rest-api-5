<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class AdminController extends ApiController
{
    /**
     * @Route("/api/admin", name="is_admin", methods={"POST"})
     */
    public function isAdmin(Request $request){
    
        $em = $this->getDoctrine()->getManager();
        $request = $this->transformJsonBody($request);
        $email = $request->get('username');
    
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('email'=>$email));
    
        error_log(print_r($user->getRoles(), true),0);
        $response = array(
            'message'=>'Roles of user email address',
            'error'=>null,
            'result'=>$user->getRoles()
        ); 
        
        return new JsonResponse($response);
    }
}
