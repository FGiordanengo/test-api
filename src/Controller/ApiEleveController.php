<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Repository\EleveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ApiEleveController extends AbstractController
{
    

    /**
     * @Route("/api/eleve/liste", name="api_eleve_findAll", methods={"GET"})
     */
    public function getEleves(EleveRepository $eleveRepository, SerializerInterface $serializer)
    {
        $eleves = $eleveRepository->findAll();

        $json = $serializer->serialize($eleves, 'json', ['groups' => 'eleve:read']);
         
        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }


    /**
     * @Route("/api/eleve/ajouter", name="api_eleve_post", methods={"POST"})
     */
    public function postEleve(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $json = $request->getContent();

        try {
            $eleve = $serializer->deserialize($json, Eleve::class, 'json', ['groups' => 'eleve:read']);

            $errors = $validator->validate($eleve);

            if(count($errors) >0) {
                return $this->json($errors, 400);
            }

            $em->persist($eleve);
            $em->flush();
            //  dd($eleve);
            $response = new JsonResponse($json, 200, [], true);
    
            return $response;
        } catch(NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }  
    }

    /**
     * @Route("/api/eleve/{id}", name="api_eleve_find_one", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getEleveById(EleveRepository $eleveRepository, SerializerInterface $serializer, $id)
    {
        try {
            $eleve = $eleveRepository->find($id);
            $json = $serializer->serialize($eleve, 'json', ['groups' => 'eleve:read']);
            
            $response = new JsonResponse($json, 200, [], true);

            return $response;
        } catch(NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }  
    }

    /**
     * @Route("/api/eleve/modifier/{id}", name="api_eleve_edit", methods={"PUT"}, requirements={"id"="\d+"})
     */
    public function update($id, Request $request,EleveRepository $eleveRepository, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        
        $jsonRecu = $request->getContent();
        try {
            $oldEleve = $eleveRepository->find($id);
            $eleve = $serializer->deserialize($jsonRecu, Eleve::class, 'json');

            $oldEleve->setNom($eleve->getNom());
            $oldEleve->setPrenom($eleve->getPrenom());
            $oldEleve->setDateDeNaissance($eleve->getDateDeNaissance());

            $em->flush();

            return $this->json($eleve, 200, [], ['groups' =>'eleve:update']);

        } catch(NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }  
    }

    /**
     * @Route("/api/eleve/supprimer/{id}", name="api_eleve_supprime", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function removeEleve(Eleve $eleve, EntityManagerInterface $em)
    {
        $em->remove($eleve);
        $em->flush();
        return new Response('ok');
    }
}
