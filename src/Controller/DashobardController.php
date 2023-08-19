<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashobardController extends AbstractController
{

    
    #[Route('/', name: 'indexPage')]
    public function index(EventRepository $eventRepository,EntityManagerInterface $entityManager): Response
    {
        $repousers = $entityManager->getRepository(User::class);

        $totalUsers = $repousers->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $repoEvent = $entityManager->getRepository(Event::class);

        $totalEvents = $repoEvent->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $today = new \DateTime();
        $beginDate = $today->format('Y-m-d');
        $todayEvent = $repoEvent->createQueryBuilder('a')
            ->where('a.beginAt >= ?1')
            ->setParameter(1, $today)
            ->select('count(a.beginAt)')
            ->getQuery()
            ->getSingleScalarResult();

        $progressEvent = $repoEvent->createQueryBuilder('a')
            ->where('a.endAt < ?1')
            ->setParameter(1, $today)
            ->select('count(a.beginAt)')
            ->getQuery()
            ->getSingleScalarResult();

        $twigData = [
            'eventT' => $totalEvents,
            'usersT' => $totalUsers,
            'todayEvent' => $todayEvent,
            'progress' => $progressEvent
        ];

        return $this->render('Dashobard/dashobard.html.twig', $twigData);
    }
}
