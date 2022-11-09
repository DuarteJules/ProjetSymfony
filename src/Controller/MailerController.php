<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/email/{id}' , name: 'mailer_show')]
    public function sendEmail(MailerInterface $mailer,string $id): Response
    {
        $email = (new Email())
            ->from('CodingRecrute@gmail.com')
            ->to('GustaveauDeVache@gmail.com')
            ->subject('Réponse à candidature')
            ->text("Vous avez été retenu pour l'offre");

        $mailer->send($email);
        return $this->render('email/confirm.html.twig');
    }
}