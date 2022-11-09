<?php
// src/Controller/CandidateController.php
namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Skill;
use App\Form\Type\CandidateType;
use App\Repository\CandidateRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CandidateController extends AbstractController
{

    #[Route('/candidate/list', name: 'candidate_list')]
    public function CandidateList(ManagerRegistry $managerRegistry): Response
    {
        $candidates = $managerRegistry->getRepository(Candidate::class)->findAll();

        return $this->renderForm('candidate/list.html.twig',[
            'candidates' => $candidates,
        ]);
    }


    #[Route('/candidate/details/{id}', name: 'candidate_details')]
    public function Profile($id, CandidateRepository $candidateRepository): Response
    {
        $candidate = $candidateRepository->find($id);
        $skills = $candidate->getSkills();

        return $this->render('candidate/profile.html.twig', [
            'candidate' => $candidate,
            'skills' => $skills
        ]);
    }


    #[Route(path: '/candidate')]
    public function new(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $candidate = new Candidate();
        $skills = $managerRegistry->getRepository(Skill::class)->findAll();


        $form = $this->createForm(CandidateType::class, $candidate);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $objectManager = $managerRegistry->getManager();
            $candidate = $form->getData();
            $objectManager->persist($candidate);
            $objectManager->flush();
            return $this->redirectToRoute('candidate_list');
        }

        return $this->renderForm('candidate/new.html.twig', [
            'form' => $form,
            'skills' => $skills,
        ]);
    }

    #[Route('/candidate/edit/{id}', name: 'edit_candidate')]
    public function EditCandidate(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $candidate = $doctrine->getRepository(Candidate::class)->find($id);
        $skills = $doctrine->getRepository(Skill::class)->findAll();
        $entityManager = $doctrine->getManager();

        $form = $this->createFormBuilder($candidate)
            ->add('name', TextType::class)
            ->add('email', TextType::class)
            ->add('skills', EntityType::class,
                ['class' => Skill::class,
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'multiple' => true,
                    'expanded' => true,
                ])
            ->add('save', SubmitType::class, ['label' => 'Edit Candidate'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $candidate = $form->getData();
            $entityManager->persist($candidate);
            $entityManager->flush();

            return $this->redirectToRoute('candidate_list');
        }

        return $this->renderForm('candidate/edit.html.twig', [
            'form' => $form,
            'skills' => $skills,
        ]);
    }

    #[Route('/candidate/delete/{id}', name: 'delete_candidate')]
    public function DeleteCandidate($id, ManagerRegistry $doctrine): Response
    {
        $candidate = $doctrine->getRepository(Candidate::class)->find($id);
        $entityManager = $doctrine->getManager();

        $entityManager->remove($candidate);

        $entityManager->flush();

        return $this->redirectToRoute('candidate_list');
    }


}
