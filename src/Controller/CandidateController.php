<?php
// src/Controller/CandidateController.php
namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Skill;
use App\Form\Type\CandidateType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CandidateController extends AbstractController
{

    #[Route(path: 'candidate/me', name: 'profile')]
    public function CandidateProfile(): Response
    {
        return $this->renderForm('candidate/profile.html.twig');
    }

    #[Route(path: '/candidate')]
    public function new(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $candidate = new Candidate();

        $form = $this->createForm(CandidateType::class, $candidate);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $objectManager = $managerRegistry->getManager();
            $objectManager->persist($candidate);
            $objectManager->flush();
            return $this->redirectToRoute('profile');
        }

        return $this->renderForm('candidate/new.html.twig', [
            'form' => $form,
        ]);
    }
}
