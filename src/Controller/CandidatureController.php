<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Candidature;
use App\Entity\Job;
use App\Repository\CandidatureRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class CandidatureController extends AbstractController
{
    #[Route('/candidature/new/{id}', name: 'new_candidature')]
    public function createCandidature(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $candidature = new Candidature();
        $entityManager = $doctrine->getManager();
        $job = $doctrine->getRepository(Job::class)->find($id);
        $candidature->setJob($job);
        $form = $this->createFormBuilder($candidature)
            ->add('job', EntityType::class,
                ['class' => Job::class,
                    'choice_value' => 'id',
                    'choice_label' => 'name',
                    'disabled' => true
                ])
            ->add('candidate', EntityType::class,
                ['class' => Candidate::class,
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                ])
            ->add('save', SubmitType::class, ['label' => 'Postulate'])
            ->getForm();

        //check if the form has been submited
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //get data from the form
            $candidature = $form->getData();
            $candidature->setName('En attente');

            //save them to the db
            $entityManager->persist($candidature);

            $entityManager->flush();

            //redirect to the Job list to see the newly added Job
            return $this->redirectToRoute('details_job',['id' => $id]);
        }

        //render the form if he hasn't been submited
        return $this->renderForm('candidature/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/candidature/details/{id}', name: 'details_candidature')]
    public function Details(ManagerRegistry $doctrine, $id) : Response
    {
        $candidature = $doctrine->getRepository(Candidature::class)->find($id);
        $candidate = $doctrine->getRepository(Candidate::class)->find($candidature->getCandidate()->getId());
        $job = $candidature->getJob();

        $jobSkills = $job->getSkills();
        $candidateSkills = $candidate->getSkills();

        return $this->render('candidature/validate.html.twig', [
            'candidature' => $candidature,
            'candidate' => $candidate,
            'jobSkills' => $jobSkills,
            'candidateSkills' => $candidateSkills,
            'job' => $job
        ]);
    }

    #[Route('/candidature/validate/{id}', name: 'validate_candidature')]
    public function Validate(ManagerRegistry $doctrine, $id, CandidatureRepository $candidatureRepository) : Response
    {
        $candidature = $doctrine->getRepository(Candidature::class)->find($id);
        $candidate = $doctrine->getRepository(Candidate::class)->find($candidature->getCandidate()->getId());
        $job = $candidature->getJob();
        foreach ($job->getCandidatures() as $i => $item){
            if($item->getId() == $candidature->getId()){
                $item->setName('Valider');
                $candidatureRepository->save($item,true);
            }
            else{
                $item->setName('Refuser');
                $candidatureRepository->save($item,true);
            }
        }
        return $this->redirectToRoute('mailer_show',['id' => $candidature->getId(),'idCandidate' => $candidate ->getId()]);
    }

    #[Route('/candidature/refuse/{id}', name: 'refuse_candidature')]
    public function Refuse(ManagerRegistry $doctrine, $id, CandidatureRepository $candidatureRepository) : Response
    {
        $candidature = $doctrine->getRepository(Candidature::class)->find($id);
        $job = $candidature->getJob();
        foreach ($job->getCandidatures() as $i => $item){
            if($item->getId() == $candidature->getId()){
                $item->setName('Refuser');
                $candidatureRepository->save($item,true);
            }

        }
        return $this->redirectToRoute('details_job',['id' => $job->getId()]);
    }
}
