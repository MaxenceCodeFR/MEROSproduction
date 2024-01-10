<?php

namespace App\Command;

use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppointementsCleanupCommand extends Command
{
    protected static $defaultName = 'app:appointements-cleanup';

    private EntityManagerInterface $entityManager;
    private CalendarRepository $calendarRepository;

    public function __construct(EntityManagerInterface $entityManager, CalendarRepository $calendarRepository)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->calendarRepository = $calendarRepository;
    }

    protected function configure()
    {
        $this->setDescription('Archives les rdvs de plus de 7 jours et suprime les rdvs archivés de plus de 60 jours');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $appointments = $this->calendarRepository->findAll();
        $currentDate = new \DateTime();

        foreach ($appointments as $appointment) {
            $interval = $appointment->getEnd()->diff($currentDate);
            $daysSinceAppointment = $interval->days;

            if ($daysSinceAppointment > 60) {
                $this->entityManager->remove($appointment);
                $output->writeln('Removed: ' . $appointment->getId());
            } elseif ($daysSinceAppointment > 7) {
                $appointment->setIsArchived(true);
                $this->entityManager->persist($appointment);
                $output->writeln('Archived: ' . $appointment->getId());
            }
        }

        $this->entityManager->flush();
        $output->writeln('Appointments cleanup complete.');

        return Command::SUCCESS;
    }
}
