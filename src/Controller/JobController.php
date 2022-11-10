<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Job;
use App\Entity\Skill;
use App\Repository\CandidateRepository;
use App\Repository\CandidatureRepository;
use App\Repository\JobRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class JobController extends AbstractController
{
    #[Route('/{id}', name: 'job_list')]
    public function JobList(JobRepository $jobRepository, CandidateRepository $candidateRepository,Request $request, $id = 0)
    {

        //get all jobs
        $job = $jobRepository->findAll();
        $candidates = $candidateRepository->findAll();

        return $this->render('job/list.html.twig', [
            'jobs' => $job,
            'candidates' => $candidates
        ]);

    }

    #[Route('/job/new', name: 'new_job')]
    public function NewJob(Request $request, JobRepository $jobRepository): Response
    {
        //create a new Job object
        $job = new Job();

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
            //save them to the db
            $jobRepository->save($job,true);

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
    public function SuppJob($id, JobRepository $jobRepository): Response
    {
        //get the job to delete
        $job = $jobRepository->find($id);

        //delete it
        $jobRepository->remove($job, true);

        //render the job list
        return $this->redirectToRoute('job_list');
    }

    #[Route('/job/details/{id}', name: 'details_job')]
    public function Details($id, JobRepository $jobRepository): Response
    {
        //get the job and his skills
        $job = $jobRepository->find($id);
        $company = $job->getCompany();
        $skills = $job->getSkills();
        $candidatures = $job->getCandidatures();
        $candidates = array();
        foreach($candidatures as $i => $item) {
            array_push($candidates,$item->getCandidate()->getName());
        }
        return $this->render('job/details.html.twig', [
            'job' => $job,
            'skills' => $skills,
            'candidatures' => $candidatures,
            'candidates' => $candidates,
            'company' => $company
        ]);
    }

    #[Route('/matching', name: 'job_matching')]
    public function Matching(Request $request, CandidatureRepository $candidatureRepository, $id = 0)
    {
        $candidatures = $candidatureRepository->findAll();
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonData = array();
        $idx = 0;
        foreach($candidatures as $candidature) {
            $temp = array(
                'name' => $candidature->getName(),
                'id' => $candidature->getId(),
            );
            $jsonData[$idx++] = $temp;
        }
//        if ($request->isXmlHttpRequest()) {
//            if($id == 0){
//                $jsonContent = $serializer->serialize($candidates, 'json');
//                return new JsonResponse($jsonContent);
//            }
//
//        }
//        $jsonContent = $serializer->serialize($candidates, 'json');
        return new JsonResponse($jsonData);
    }
}
