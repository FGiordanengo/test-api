<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Eleve;
use App\Repository\NoteRepository;
use App\Repository\EleveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ApiNoteController extends AbstractController
{
    /**
     * @Route("/api/note/{id}", name="api_note_find_one", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getNoteById(noteRepository $noteRepository, SerializerInterface $serializer, $id)
    {
        
        $note = $noteRepository->find($id);
        // dd($note);
        $json = $serializer->serialize($note, 'json', ['groups' => 'note:read']);
         
        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }

    /**
     * @Route("/api/note/moyenne/{id}", name="api_moyenne_find_one", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getMoyenneByEleveId(EleveRepository $eleveRepository, SerializerInterface $serializer, $id)
    {
        
        $eleve = $eleveRepository->find($id);

        $json = $serializer->serialize($eleve, 'json', ['groups' => 'eleve:read']);
        $person = $serializer->deserialize($json,Eleve::class,'json');
        // dd($person->getNotes()[1]->getValeur());
        $sommeNotes = 0;
        if (!count($person->getNotes()) == 0) {
            foreach ($person->getNotes() as $note) {
                $val = $note->getValeur();
                $sommeNotes = ($sommeNotes + $val);
            }
            $moyenneGenerale = round($sommeNotes / count($person->getNotes()), 2);

            $response = new JsonResponse($moyenneGenerale, 200, [], true);

            return $response;
        } else {
            return $this->json([
                'status' => 400,
                'message' => "Cet élève n'a aucune notes ou n'existe pas"
            ], 400);
        }
    }

    /**
     * @Route("/api/note/moyenne-generale", name="api_note_index", methods={"GET"})
     */
    public function getMoyenneGenerale(NoteRepository $noteRepository, SerializerInterface $serializer)
    {
        $notes = $noteRepository->findAll();
        $sommeNotes = 0;
        foreach($notes as $note) {
            $val = $note->getValeur();
            $sommeNotes = ($sommeNotes + $val);
        }
        $moyenneGenerale = round($sommeNotes / count($notes), 2);

        $json = $serializer->serialize($moyenneGenerale, 'json', ['groups' => 'note:moyenne-generale']);

        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }

    /**
     * @Route("/api/note/ajouter", name="api_note_post", methods={"POST"})
     */
    public function postNote(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $json = $request->getContent();

        try {
            $note = $serializer->deserialize($json, Note::class, 'json', ['groups' => 'note:post']);

            $errors = $validator->validate($note);

            if(count($errors) >0) {
                return $this->json($errors, 400);
            }

            $em->persist($note);
            $em->flush();
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
     * @Route("/api/note/add/{id}", name="api_note_add_one", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function postNoteByEleveId(Request $request, EleveRepository $eleveRepository, SerializerInterface $serializer, EntityManagerInterface $em, $id)
    {
        
        $eleve = $eleveRepository->find($id);
        $jsonRecu = $request->getContent();
        $noteRecue = $serializer->deserialize($jsonRecu, Note::class, 'json');

        // $json = $serializer->serialize($eleve, 'json', ['groups' => 'eleve:read']);
        // dd($eleve);
        $note = new Note();
        $note->setValeur($noteRecue->getValeur());
        $note->setMatiere($noteRecue->getMatiere());
        $note->setEleve($eleve);
        $em->persist($note);
        $em->flush();
        $response = new JsonResponse($jsonRecu, 200, [], true);

        return $response;
    }
}
