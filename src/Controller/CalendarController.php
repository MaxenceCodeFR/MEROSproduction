<?php

namespace App\Controller;


use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/calendar')]
class CalendarController extends AbstractController
{
    #[Route('/', name: 'calendar')]
    public function calendar(CalendarRepository $calendar): Response
    {
        $events = $calendar->findBy(['user' => $this->getUser()]);

        $meets = [];
        foreach ($events as $event) {
            $meets[] = [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
                'allDay' => $event->isAllDay(),
                'isArchived' => $event->isIsArchived() ? 'true' : 'false',
                'user' => $event->getUser()->getId(),
            ];
        }

        $data = json_encode($meets);

        return $this->render('calendar.html.twig', compact('data'));
    }
    #[Route('/index', name: 'app_calendar_index', methods: ['GET'])]
    public function index(CalendarRepository $calendarRepository): Response
    {
        return $this->render('calendar/index.html.twig', [
            'calendars' => $calendarRepository->findBy(['user' => $this->getUser()]),
        ]);
    }

    #[Route('/new', name: 'app_calendar_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $calendar = new Calendar();
        $form = $this->createForm(CalendarType::class, $calendar, [
            'include_influencer' => true,

        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $calendar->setUser($this->getUser());
            $calendar->setIsArchived(false);
            $entityManager->persist($calendar);
            $entityManager->flush();

            return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('calendar/new.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_calendar_show', methods: ['GET'])]
    public function show(Calendar $calendar): Response
    {
        return $this->render('calendar/show.html.twig', [
            'calendar' => $calendar,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_calendar_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Calendar $calendar, 
        EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('calendar/edit.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_calendar_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Calendar $calendar, 
        EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $calendar->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Ce contrat à été supprimé avec succès.');
            $entityManager->remove($calendar);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
    }

    //!CETTE METHODE EST UTILISER DANS UNE COMMANDE (CRON TASK)
    // public function manageAppointements(EntityManagerInterface $em, CalendarRepository $calendar): Response
    // {
    //     $appointements = $calendar->findAll();
    //     $currentDate = new \DateTime();

    //     foreach ($appointements as $appointement) {
    //         // Assurez-vous que getEnd() renvoie un objet DateTime
    //         if (!$appointement->getEnd()) {
    //             continue; // Ou gérer l'erreur comme vous le souhaitez
    //         }

    //         $interval = $appointement->getEnd()->diff($currentDate);
    //         $daysSinceAppointment = $interval->days; // Obtenez le nombre de jours

    //         if ($daysSinceAppointment > 60) {
    //             $em->remove($appointement);
    //         } elseif ($daysSinceAppointment > 7) {
    //             $appointement->setIsArchived(true);
    //             $em->persist($appointement);
    //         }
    //     }

    //     $em->flush();
    //     return new Response('ok');
    // }
}
