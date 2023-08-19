<?php

namespace App\Controller;

use DateTime;
use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function index(EventRepository $eventRepository,PaginatorInterface $paginator,Request $request): Response
    {
        $event=$paginator->paginate(
            $eventRepository->findAll(),
            $request->query->getInt('page',1),
            10
        );
        return $this->render('event/index.html.twig', [
            'events' => $event,
        ]);
    }


    #[Route('/calendar', name: 'booking_calendar')]
    public function calendar(EventRepository $rep): Response
    {
       $events=$rep->findAll();
       $calendrier=[];
       foreach($events as $event){
        $calendrier[]=[
            'id' =>$event->getId(),
            'start' =>$event->getBeginAt()->format('Y-m-d H:i:s'),
            'end' =>$event->getEndAt()->format('Y-m-d H:i:s'),
            'title' =>$event->getTitle(),
            'location' =>$event->getLocation(),
           
            
        ];
       }
       $data=json_encode($calendrier);
        return $this->render('event/calendar.html.twig',compact('data'));
    }
   
    #[Route('/new', name: 'event_new')]
    public function create(ManagerRegistry $doctrine,Request $request): Response
    {
        
        $event=new Event();
        $form=$this->createForm(EventType::class,$event);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em=$doctrine->getManager();
            $em->persist($event);
            $em->flush();
          return $this->redirectToRoute('list');
        }

    return $this->render('event/new.html.twig', [
        'form'=>$form->createView()
     ]);

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('dateStart', 'datetime', array(
            'widget' => 'single_text',
            'html5' => false,
        ));
        $builder
            ->add('task', TextType::class)
            ->add('dueDate', DateType::class)
            ->add('save', SubmitType::class)
        ;

    }

    #[Route('/edit/{id}', name: 'event_edit')]
    public function edit(Request $request, Event $event,ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em=$doctrine->getManager();
            $em->flush();
           

            return $this->redirectToRoute('list');
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }


    private $entityManager;
    private $csrfTokenManager;

    public function __construct(EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->entityManager = $entityManager;
        $this->csrfTokenManager = $csrfTokenManager;
    }




    #[Route('/delete/{id}', name: 'event_delete')]
    public function delete(Request $request, Event $event,ManagerRegistry $doctrine): Response
    {

  
        $csrfToken = $request->request->get('_token');

        if ($this->csrfTokenManager->isTokenValid(new CsrfToken('delete'.$event->getId(), $csrfToken))) {
            $this->entityManager->remove($event);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('list');
        

       
    }


    #[Route('/show/{id}', name: 'event_show')]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }




    
       
}
