<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Job;
use App\Entity\Skill;
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
        //get all companies
        $job = $doctrine->getRepository(Job::class)->findAll();

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
            foreach($job->getSkills()->getIterator() as $i => $item) {
                $skill = $doctrine->getRepository(Skill::class)->find($item->getId());
                $skill->addJob($job);
                $entityManager->persist($skill);
            }
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
    public function EditJob(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        //create a new Job object
        $job = $doctrine->getRepository(Job::class)->find($id);

        
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
        return $this->renderForm('job/edit.html.twig', [
            'form' => $form,
            'job' => $job
        ]);
    }

    #[Route('/job/del/{id}', name: 'del_job')]
    public function SuppJob($id,ManagerRegistry $doctrine) : Response
    {
        //get the company to delete
        $job = $doctrine->getRepository(Company::class)->find($id);
        $entityManager = $doctrine->getManager();

        //delete it
        $entityManager->remove($job);

        //save changes
        $entityManager->flush();

        //render the company list
        return $this->redirectToRoute('company_list');
    }
}
