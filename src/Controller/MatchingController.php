<?php

namespace App\Controller;

    use App\Repository\CandidateRepository;
    use App\Repository\JobRepository;
    use App\Service\MatchingService;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    class MatchingController extends AbstractController{
        #[Route('/matching/{$id}', name: '')]

        public function matching($id, MatchingService $matchingService, JobRepository $jobRepository,CandidateRepository $candidateRepository,
        ){

        $matchingService->foundMatching($id, $jobRepository, $candidateRepository);


        }
    }