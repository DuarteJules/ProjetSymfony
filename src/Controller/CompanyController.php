<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\Type\JobType;



class CompanyController extends AbstractController
{
    #[Route('/company', name: 'company_list')]
    public function CompanyList(ManagerRegistry $doctrine): Response
    {
        $companies = $doctrine->getRepository(Company::class)->findAll();

        return $this->render('company/list.html.twig', [
            'companies' => $companies,
        ]);
    }

    #[Route('/company/new', name: 'new_company')]
    public function NewCompany(Request $request, ManagerRegistry $doctrine) : Response
    {
        $company = new Company();
        $entityManager = $doctrine->getManager();

        $job = $doctrine->getRepository(Job::class)->findAll();
        $form = $this->createFormBuilder($company)
            ->add('name', TextType::class)
            ->add('jobs', ChoiceType::class, ['choices' => $job])
            ->add('save', SubmitType::class, ['label' => 'Create Company'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $company = $form->getData();

            $entityManager->persist($company);

            $entityManager->flush();

            return $this->redirectToRoute('company_list');
        }

        return $this->renderForm('company/new.html.twig', [
            'form' => $form,
        ]);
    }
}
