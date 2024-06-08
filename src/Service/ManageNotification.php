<?php

namespace App\Service;

use App\Entity\ContactCompany;
use App\Entity\ContactInfluencer;

use App\Entity\Notification;
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
        //Si l'entité est une instance de ContactCompany ou ContactInfluencer
        if ($entity instanceof ContactCompany || $entity instanceof ContactInfluencer) {
            $notification = $entity->getNotification(); //alors on récupère la notification
        }

        //Si la notification existe
        if ($notification) {
            $notification->setIsNew(false);//on met à jour le statut de la notification
            $notification->setIsSeen(true); //on met à jour le statut de la notification

            //puisque quand on clique sur la route associé à la notification/service, on met à jour le statut de la notification

            //Si la notification n'est pas nouvelle et qu'elle est vue 
            if (!$notification->isIsNew() && $notification->isIsSeen()) {
                $entity->setNotification(null); //on supprime la notification
                $this->em->remove($notification);
            }

            $this->em->flush();
        }

}
}
