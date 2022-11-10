<?php

namespace App\Controller;


    use App\Service\MatchingService;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    class MatchingController extends AbstractController{
        #[Route('/matching/{$id}', name: '')]

        public function matching($id, MatchingService $matchingService){

        $jobMatching = $matchingService->foundMatching($id);


        }
    }