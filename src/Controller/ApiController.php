<?php

namespace App\Controller;

use DateTime;
use App\Entity\Event;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

       /**
 * @Route("/api/{id}/edit", name="api_event_edit", methods={"PUT"})
 */
public function majEvent(?Event $event, Request $request,ManagerRegistry $doctrine)
{
    // On récupère les données
    $donnees = json_decode($request->getContent());

    if(
        isset($donnees->title) && !empty($donnees->title) &&
        isset($donnees->beginAt) && !empty($donnees->beginAt) &&
        isset($donnees->endAt) && !empty($donnees->endAt) &&
        isset($donnees->location) && !empty($donnees->location) 
        
    ){
        // Les données sont complètes
        // On initialise un code
        $code = 200;

        // On vérifie si l'id existe
        if(!$event){
            // On instancie un rendez-vous
            $event = new Event;

            // On change le code
            $code = 201;
        }

        // On hydrate l'objet avec les données
        $event->setTitle($donnees->title);
        $event->setLocation($donnees->location);
        $event->setBeginAt(new DateTime($donnees->beginAt));
        $event->setendAt(new DateTime($donnees->endAt));
       
        

        $em = $doctrine()->getManager();
        $em->persist($event);
        $em->flush();

        // On retourne le code
        return new Response('Ok', $code);
    }else{
        // Les données sont incomplètes
        return new Response('Données incomplètes', 404);
    }


    return $this->render('api/index.html.twig', [
        'controller_name' => 'ApiController',
    ]);
}
}
