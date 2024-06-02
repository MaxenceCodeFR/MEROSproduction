<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class EmailService {
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmailFromNoReply(string $to, string $subject, string $template, array $context) {

        $email = (new TemplatedEmail())
            ->from('no-reply@meros-production.fr')
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);
        $this->mailer->send($email);    
    }
}