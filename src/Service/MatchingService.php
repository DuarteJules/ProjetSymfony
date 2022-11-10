<?php

namespace App\Service;

use App\Entity\Job;
use App\Repository\CandidateRepository;
use App\Repository\JobRepository;

class MatchingService
{
    private $jobRepository;
    private $candidateRepository;

    public function __construct(
        JobRepository $jobRepository,
        CandidateRepository $candidateRepository
    ){
        $this->jobRepository = $jobRepository;
        $this->candidateRepository=$candidateRepository;
    }

    public function foundMatching(
        $id,
    ): array
    {
        $count = 0;
        $offersArray = [];
        #get all job offer
        $offers = $this->jobRepository->findAll();


        #get the candidate's skills
        $candidate = $this->candidateRepository->find($id);
        $candidateSkills[] = $candidate->getSkills();

        foreach ($offers as $offer) {
            {
                if ($candidateSkills != $offer->getSkills()) {
                }elseif ($count>=2){
                    $offersArray[] = $offer;
                } else {
                    $count = $count+1;
                }
            }
        }
        return $offersArray;
    }
}
        #compare offers.skill and candidate.skills
        #if candidate.skills !== offer.skills[i] => {return 'osef'}
        #else{return $offers[i]

