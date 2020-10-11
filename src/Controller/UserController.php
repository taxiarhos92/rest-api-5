<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends ApiController
{

   /**
    * @Route("/api/users/get", name="get_users", methods={"GET"})
    */
    public function getUsers(){
        $users = $this->getDoctrine()->getRepository(User::class)->transformAll();
        if(empty($users)){
            return $this->respondNotFound();
        }

        return $this->response($users);   
    }

   /**
    * @Route("/api/user/update", name="update_user", methods={"PUT"})
    */
    public function update(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->transformJsonBody($request);
        $firstName = $request->get('firstName');
        $lastName = $request->get('lastName');
        $email = $request->get('username');
        $password = $request->get('password');
        

        if (empty($firstName) || empty($lastName) || empty($password)){
        return $this->respondValidationError("Empty Fisrtname, Lastname or Password ");
        }

        
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('email'=>$email));

        try{
            $user->setPassword($encoder->encodePassword($user, $password));
            $user->setLastName($lastName);
            $user->setFirstName($firstName);
            $em->persist($user);
            $em->flush();

            return $this->respondWithSuccess(sprintf('User %s successfully Updated', $user->getEmail()));

        }catch(\Exception $e){
            return $this->respondValidationError();
        }
    }
    
}