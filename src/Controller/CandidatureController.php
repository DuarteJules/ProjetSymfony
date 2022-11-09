<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Candidature;
use App\Entity\Job;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CandidatureController extends AbstractController
{
    #[Route('/candidature/new/{id}', name: 'new_candidature')]
    public function createCandidature(Request $request, ManagerRegistry $managerRegistry, $id): Response
    {
        $candidature = new Candidature();
        $entityManager = $managerRegistry->getManager();

        $form = $this->createFormBuilder($candidature)
            ->add('job', EntityType::class,
                ['class' => Job::class,
                    'choice_value' => 'id',
                    'choice_label' => 'name'
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
}
