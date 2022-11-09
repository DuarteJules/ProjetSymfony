<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/email/{id}')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('candidature@gmail.com')
            ->to(get('id'))
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Réponse à candidature')
            ->text("Vous avez été retenu pour l'offre")

        $mailer->send($email);
        return 'message send';
    }
}