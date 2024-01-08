<?php

namespace App\Controller;

use App\Entity\Calendar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }
    #[Route('/api/{id}/edit', name: 'api_event_edit', methods: ['PUT'])]
    public function majEvent(?Calendar $calendar, Request $request, EntityManagerInterface $em): Response
    {
        //on récupère les données
        $data = json_decode($request->getContent());
        //on vérifie si l'event existe
        if (
            isset($data->title) && !empty($data->title) &&
            isset($data->start) && !empty($data->start) &&
            isset($data->end) && !empty($data->end) &&
            isset($data->description) && !empty($data->description) &&
            isset($data->backgroundColor) && !empty($data->backgroundColor) &&
            isset($data->borderColor) && !empty($data->borderColor) &&
            isset($data->textColor) && !empty($data->textColor)
        ) {
            //Les données sont complètes
            $code = 200;

            if (!$calendar) {
                //L'event n'existe pas
                $calendar = new Calendar();
                $code = 201;
            }

            //On hydrate l'objet
            $calendar->setTitle($data->title);
            $calendar->setStart(new \DateTime($data->start));
            if ($data->allDay) {
                $calendar->setEnd(new \DateTime($data->start));
            } else {
                $calendar->setEnd(new \DateTime($data->end));
            }
            $calendar->setDescription($data->description);
            $calendar->setBackgroundColor($data->backgroundColor);
            $calendar->setBorderColor($data->borderColor);
            $calendar->setTextColor($data->textColor);
            $calendar->setAllDay($data->allDay);
        } else {
            //Les données sont incomplètes
            return new Response('Données incomplètes', 404);
        }

        //On sauvegarde en BDD
        $em->persist($calendar);
        $em->flush();

        return new Response('Ok', $code);
    }
}
