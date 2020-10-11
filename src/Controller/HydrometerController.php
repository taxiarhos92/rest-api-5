<?php

namespace App\Controller;

use App\Entity\Hydrometer;
use App\Repository\HydrometerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HydrometerController extends ApiController
{

    /**
     * @param HydrometerRepository $hydrometerRepository
     * @return JsonResponse
     * @Route("/api/hydrometers", name="hydrometers_list", methods={"GET"})
     */
    public function list(HydrometerRepository $hydrometerRepository)
    {
        //$hydrometers = $this->getDoctrine()->getRepository(Hydrometer::class)->transformAll();
        $hydrometers = $hydrometerRepository->transformAll();
        if(empty($hydrometers)){
            return $this->respondNotFound();
        }

        return $this->response($hydrometers);
    }

    /**
     * @Route("/api/hydrometer/add", name="hydrometer_add", methods={"POST"})
     */
    public function add(Request $request,ValidatorInterface $validator)
    {
        try{ 
            $request = $this->transformJsonBody($request);

            if(! $request){
                return $this->respondValidationError('Please provide a valid request!');
            }

            $hydrometer = new Hydrometer();
            $hydrometer->setOwner($request->get('Owner'));

            //symfony entity validation start
            $errors = $validator->validate($hydrometer);

            foreach($errors as $error){
                $errorResponse = array(
                    'field'=> $error->getPropertyPath(),
                    'message'=> $error->getMessage()
                );
            }

            if (count($errors) > 0) {
                $response_error = array(
                    'message'=>'Validation error',
                    'error'=>$errorResponse
                );
                return $this->respondValidationError($response_error);
            }
            //symfony entity validation end

            $em = $this->getDoctrine()->getManager();
            $em->persist($hydrometer);
            $em->flush();

            $lastId = $hydrometer->getId();

            $response = array(
                'message'=>'Inserted successfully',
                'lastId'=> $lastId,
                'error'=>null,
            );

            return $this->respondCreated($response);
        }catch(\Exception $e){
            return $this->respondValidationError();
        }

    }

    /**
     * @Route("/api/hydrometer/{id}", name="update_hydrometer", methods={"PUT"})
     */
    public function updateHydrometer($id, Request $request, ValidatorInterface $validator)
    {   
        try{
            $request = $this->transformJsonBody($request);

            if(! $request){
                return $this->respondValidationError('Please provide a valid request!');
            }


            $hydrometer = $this->getDoctrine()->getRepository(Hydrometer::class)->find($id);

            if(empty($hydrometer)){
                $response = array(
                    'message'=>'Δε βρέθηκε Υδρόμετρο',
                    'error'=>null,
                    'result'=>null
                );
    
                return new JsonResponse($response, Response::HTTP_NOT_FOUND);
            }

            $hydrometer->setOwner($request->get('Owner'));

            //symfony entity validation start
            $errors = $validator->validate($hydrometer);

            foreach($errors as $error){
                $errorResponse = array(
                    'field'=> $error->getPropertyPath(),
                    'message'=> $error->getMessage()
                );
            }

            if (count($errors) > 0) {
                $response_error = array(
                    'message'=>'Validation error',
                    'error'=>$errorResponse
                );
                return $this->respondValidationError($response_error);
            }
            //symfony entity validation end

            $em = $this->getDoctrine()->getManager();
            $em->persist($hydrometer);
            $em->flush();

            $response = array(
                'message'=>'Updated successfully',
                'error'=>null,
                'result'=>null
            );
        
            return $this->respondWithSuccess($response);

        }catch(\Exception $e){
            return $this->respondValidationError();
        }
    }

    /**
     * @Route("/api/hydrometer/{id}", name="show_hydrometer", methods={"GET","HEAD"})
     */
    public function showHydrometer($id)
    {  
        $hydrometer = $this->getDoctrine()->getRepository(Hydrometer::class)->find($id);
        
        if(empty($hydrometer)){
            return $this->respondNotFound();
        }

        $response = json_decode($this->get('serializer')->serialize($hydrometer,'json'));

        return $this->response($response);
    }

    /**
     * @Route("/api/hydrometer/{id}", name="delete_hydrometer", methods={"DELETE","HEAD"})
     */
    public function deleteHydrometer($id)
    {   
        $hydrometer = $this->getDoctrine()->getRepository(Hydrometer::class)->find($id);

        if(empty($hydrometer)){ 
            $response = array(
                'message'=>'Δε βρέθηκε Υδρόμετρο',
                'error'=>null,
                'result'=>null
            );      

            return $this->respondWithErrors($response);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($hydrometer);
        $em->flush();

        $response = array(
            'message'=>'Hydrometer deleted',
            'error'=>null,
            'result'=>null
        );

        return $this->respondWithSuccess($response);
    }
}
