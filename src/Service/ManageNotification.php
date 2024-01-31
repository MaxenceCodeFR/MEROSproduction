<?php

namespace App\Service;

use App\Entity\ContactCompany;
use App\Entity\ContactInfluencer;

use Doctrine\ORM\EntityManagerInterface;

class ManageNotification
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function updateNotificationStatus($entity): void
    {
        $notification = null;

        if ($entity instanceof ContactCompany || $entity instanceof ContactInfluencer) {
            $notification = $entity->getNotification();
        }

        if ($notification) {
            $notification->setIsNew(false);
            $notification->setIsSeen(true);

            if (!$notification->isIsNew() && $notification->isIsSeen()) {
                $entity->setNotification(null);
                $this->em->remove($notification);
            }

            $this->em->flush();
        }
}

    // Vous pouvez ajouter d'autres méthodes liées aux notifications ici.
}
