<?php
// src/Controller/CandidateController.php
namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Skill;
use App\Form\Type\CandidateType;
use App\Repository\CandidateRepository;
use App\Repository\CandidatureRepository;
use App\Repository\JobRepository;
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

    #[Route('/candidate/candidature/{id}', name: 'candidate_candidature')]
    public function JobListWait(CandidateRepository $candidateRepository, CandidatureRepository $candidatureRepository,JobRepository $jobRepository, int $id): Response
    {
        //get all jobs
        $candidat = $candidateRepository->find($id);
        $candidatureWait = $candidatureRepository->findBy(array("candidate" => $candidat));

        //render the job list
        return $this->render('candidate/wait.html.twig', [
            'candidat' => $candidat,
            'candidatureWaits' => $candidatureWait,
        ]);
    }

    #[Route('/candidate/notmatch/{id}', name: 'candidate_notmatch')]
    public function JobListNotMatch(CandidateRepository $candidateRepository, JobRepository $jobRepository, int $id): Response
    {
        $counter = 0;
        $verifMatch = 0;
        $candidat = $candidateRepository->find($id);
        $skills = $candidat->getSkills();
        $jobs = $jobRepository->findAll();
        $jobsNotMatch[0] = $jobs[0];
        foreach ($jobs as $job)

        {
            $verifMatch = 0;
            foreach ($job->getSkills() as $skilljob)
            {
                foreach ($skills as $skill)
                {
                    if ($skilljob == $skill)
                    {
                        $verifMatch++;
                    }
                }
            }
            if ($verifMatch < 2)
            {
                $jobsNotMatch[$counter] = $job;
                $counter++;
            }
        }

        //render the job list
        return $this->render('candidate/notmatch.html.twig', [
            'candidat' => $candidat,
            'jobsNotMatch' => $jobsNotMatch,
        ]);
    }


    #[Route(path: '/candidate', name: 'new_candidate')]
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

    #[Route('/candidate/skills/edit/{id}', name: 'candidate_skills_add_edit_remove')]
    public function AddEditDeleteSkillsCandidate(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $candidate = $doctrine->getRepository(Candidate::class)->find($id);
        $skills = $doctrine->getRepository(Skill::class)->findAll();
        $entityManager = $doctrine->getManager();

        $form = $this->createFormBuilder($candidate)
            ->add('name', TextType::class)
            ->add('skills', EntityType::class,
                ['class' => Skill::class,
                    'choice_label' => 'name',
                    'multiple' => true,
                    'expanded' => true,
                ])
            ->add('save', SubmitType::class, ['label' => 'Add / Edit / Delete Skills'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $candidate = $form->getData();
            $entityManager->persist($candidate);
            $entityManager->flush();

            return $this->redirectToRoute('candidate_list');
        }

        return $this->renderForm('candidate/skills/addmodifydelete.html.twig', [
            'form' => $form,
            'skills' => $skills,
        ]);
    }
}
