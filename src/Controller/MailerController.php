<?php
namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Candidature;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/email/{idCandidate}/{id}' , name: 'mailer_show')]
    public function sendEmail(ManagerRegistry $doctrine,MailerInterface $mailer,int $idCandidate, int $id): Response
    {
        $candidature = $doctrine->getRepository(Candidature::class)->find($id);
        $candidate = $doctrine->getRepository(Candidate::class)->find($idCandidate);
        $job = $candidature->getJob();
        $namecompany = $job->getCompany()->getname();
        $namejob = $job->getName();
        $mail = $candidate -> getEmail();
        $email = (new Email())
            ->from('CodingRecrute@gmail.com')
            ->to($mail)
            ->subject('Réponse à candidature')
            ->text("Vous avez été retenu pour l'offre intitulé " . $namejob . " posté par " . $namecompany . ".");

        $mailer->send($email);
        return $this->redirectToRoute('details_job',['id' => $job->getId()]);
    }
}