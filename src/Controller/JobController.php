<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Job;
use App\Entity\Skill;
use App\Repository\JobRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class JobController extends AbstractController
{
    #[Route('/', name: 'job_list')]
    public function JobList(ManagerRegistry $doctrine): Response
    {
        //get all jobs
        $job = $doctrine->getRepository(Job::class)->findAll();

        //render the job list
        return $this->render('job/list.html.twig', [
            'jobs' => $job,
        ]);
    }

    #[Route('/job/new', name: 'new_job')]
    public function NewJob(Request $request, ManagerRegistry $doctrine): Response
    {
        //create a new Job object
        $job = new Job();
        $entityManager = $doctrine->getManager();

        //create a new form for the new Job object
        $form = $this->createFormBuilder($job)
            ->add('name', TextType::class)
            ->add('company', EntityType::class,
                ['class' => Company::class,
                    'choice_value' => 'id',
                    'choice_label' => 'name'
                ])
            ->add('skills', EntityType::class,
                ['class' => Skill::class,
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'multiple' => true,
                    'expanded' => true,
                ])
            ->add('save', SubmitType::class, ['label' => 'Create Job'])
            ->getForm();

        //check if the form has been submited
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //get data from the form
            $job = $form->getData();
            $entityManager->persist($job);
            //save them to the db
            $entityManager->flush();

            //redirect to the Job list to see the newly added Job
            return $this->redirectToRoute('job_list');
        }

        //render the form if he hasn't been submited
        return $this->renderForm('job/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/job/edit/{id}', name: 'edit_job')]
    public function EditJob(Request $request, JobRepository $jobRepository, $id): Response
    {
        //get the job object to edit
        $job = $jobRepository->find($id);
        //create a new form for the new Job object
        $form = $this->createFormBuilder($job)
            ->add('name', TextType::class)
            ->add('company', EntityType::class,
                ['class' => Company::class,
                    'choice_value' => 'id',
                    'choice_label' => 'name'
                ])
            ->add('skills', EntityType::class,
                ['class' => Skill::class,
                    'choice_label' => 'name',
                    'multiple' => true,
                    'expanded' => true,
                ])
            ->add('save', SubmitType::class, ['label' => 'Modifier le Job'])
            ->getForm();

        //check if the form has been submited
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //get data from the form
            $job = $form->getData();

            $jobRepository->save($job, true);

            //redirect to the Job list to see the newly modified Job
            return $this->redirectToRoute('job_list');
        }

        //render the form if he hasn't been submited
        return $this->renderForm('job/edit.html.twig', [
            'form' => $form,
            'job' => $job
        ]);
    }

    #[Route('/job/del/{id}', name: 'del_job')]
    public function SuppJob($id, ManagerRegistry $doctrine): Response
    {
        //get the job to delete
        $job = $doctrine->getRepository(Job::class)->find($id);
        $entityManager = $doctrine->getManager();

        //delete it
        $entityManager->remove($job);

        //save changes
        $entityManager->flush();

        //render the job list
        return $this->redirectToRoute('job_list');
    }

    #[Route('/job/details/{id}', name: 'details_job')]
    public function Details($id, JobRepository $jobRepository): Response
    {
        //get the job and his skills
        $job = $jobRepository->find($id);
        $skills = $job->getSkills();

        return $this->render('job/details.html.twig', [
            'job' => $job,
            'skills' => $skills
        ]);
    }
}
