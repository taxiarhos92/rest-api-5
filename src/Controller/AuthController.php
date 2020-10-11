<?php
 
 namespace App\Controller;


 use App\Entity\User;
 use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\HttpFoundation\Response;
 use Symfony\Component\Routing\Annotation\Route;
 use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
 use Symfony\Component\Security\Core\User\UserInterface;
 use Symfony\Component\HttpFoundation\Cookie;

 class AuthController extends ApiController
 {

  public function register(Request $request, UserPasswordEncoderInterface $encoder)
  {
   $em = $this->getDoctrine()->getManager();
   $request = $this->transformJsonBody($request);
   $firstName = $request->get('firstName');
   $lastName = $request->get('lastName');
   $email = $request->get('username');
   $password = $request->get('password');
   

   if (empty($password) || empty($email)){
    return $this->respondValidationError("Invalid Username or Password or Email");
   }

    
    $user = $this->getDoctrine()->getRepository(User::class)->findBy(array('email'=>$email));
    if($user){
        $response = array(
            'message'=>'Duplicate email address',
            'error'=>null,
            'result'=>null
        );      

        return $this->respondDuplicateError($response);
    } 

    try{
        $user = new User($email);
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setEmail($email);
        $user->setLastName($lastName);
        $user->setFirstName($firstName);
        $em->persist($user);
        $em->flush();

        return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getEmail()));

    }catch(\Exception $e){
        return $this->respondValidationError();
    }
}


public function getTokenAndRolesUser(Request $request, UserPasswordEncoderInterface $encoder, JWTTokenManagerInterface $JWTManager){
    $em = $this->getDoctrine()->getManager();
    $request = $this->transformJsonBody($request);
    $email = $request->get('username');
    $password = $request->get('password');
    
    $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('email'=>$email));

    if(!$user){
        $error = array(
            'error_message'=>'Check your credentials. Wrong e-mail address'
        );

        return $this->respondValidationError($error);
    }

    if($encoder->isPasswordValid($user, $password)){
        $response = array(
            'token'=> $JWTManager->create($user),
            'roles'=> $user->getRoles(),
            
            
        );

        /* 
        $res = new Response();

        $res->setContent(json_encode($response));
        $res->headers->set('Content-Type', 'application/json');
        
        $res->headers->setCookie(Cookie::create('BEARER',$JWTManager->create($user), time()+60*60*24*15, '/',null, false));       
        
        return new Response($res);
        */
        return new JsonResponse($response);
    }

    $error = array(
        'error_message'=>'Incorect Password'
    ); 

    return $this->respondValidationError($error);
 
  }

  /**
   * @param UserInterface $user
   * @param JWTTokenManagerInterface $JWTManager
   * @return JsonResponse
   */
  public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager)
  {
   return new JsonResponse(['token' => $JWTManager->create($user)]);
  }

}

