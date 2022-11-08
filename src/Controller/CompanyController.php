<?php

namespace App\Controller;

use App\Entity\Company;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\Persistence\ManagerRegistry;



class CompanyController extends AbstractController
{
    #[Route('/company', name: 'company_list')]
    public function CompanyList(ManagerRegistry $doctrine): Response
    {
        //get all companies
        $companies = $doctrine->getRepository(Company::class)->findAll();

        //render the company list
        return $this->render('company/list.html.twig', [
            'companies' => $companies,
        ]);
    }

    #[Route('/company/new', name: 'new_company')]
    public function NewCompany(Request $request, ManagerRegistry $doctrine) : Response
    {
        //create a new company object
        $company = new Company();
        $entityManager = $doctrine->getManager();

        //create a new form for the new company object
        $form = $this->createFormBuilder($company)
            ->add('name', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Company'])
            ->getForm();

        //check if the form has been submited
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            //get data from the form
            $company = $form->getData();

            $entityManager->persist($company);

            //save them to the db
            $entityManager->flush();

            //redirect to the company list to see the newly added company
            return $this->redirectToRoute('company_list');
        }

        //render the form if he hasn't been submited
        return $this->renderForm('company/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/company/edit/{id}', name: 'edit_company')]
    public function EditCompany(Request $request,ManagerRegistry $doctrine, $id) : Response
    {
        //get the company to edit
        $company = $doctrine->getRepository(Company::class)->find($id);
        $entityManager = $doctrine->getManager();

        //create a form to edit the company
        $form = $this->createFormBuilder($company)
            ->add('name', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Edit Company'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            //get the edited company data
            $company = $form->getData();


            $entityManager->persist($company);

            //save them to the db
            $entityManager->flush();

            //render the company list to see the modified company
            return $this->redirectToRoute('company_list');
        }

        //render the form if he hasn't been submited
        return $this->renderForm('company/edit.html.twig', [
            'form' => $form,
            'company' => $company
        ]);
    }

    #[Route('/company/del/{id}', name: 'del_company')]
    public function SuppCompany($id,ManagerRegistry $doctrine) : Response
    {
        //get the company to delete
        $company = $doctrine->getRepository(Company::class)->find($id);
        $entityManager = $doctrine->getManager();

        //delete it
        $entityManager->remove($company);

        //save changes
        $entityManager->flush();

        //render the company list
        return $this->redirectToRoute('company_list');
    }
}
