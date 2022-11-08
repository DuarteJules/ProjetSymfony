<?php
// src/Controller/SkillController.php
namespace App\Controller;

use App\Entity\Skill;
use App\Form\Type\SkillType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SkillController extends AbstractController
{
    #[Route(path: 'skills/list', name: 'list_skills')]
    public function SkillList(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $skill = $managerRegistry->getRepository(Skill::class)->findAll();
        return $this->renderForm('skill/list.html.twig', [
            'skills' => $skill,
        ]);
    }


    #[Route(path: '/skills')]
    public function new(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $skill = new Skill();

        $form = $this->createForm(SkillType::class, $skill);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $objectManager = $managerRegistry->getManager();
            $objectManager->persist($skill);
            $objectManager->flush();
            return $this->redirectToRoute('list_skills');
        }

        return $this->renderForm('skill/new.html.twig', [
            'form' => $form,
        ]);
    }
}
