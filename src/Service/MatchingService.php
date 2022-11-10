<?php

namespace App\Service;

use App\Repository\CandidateRepository;
use App\Repository\JobRepository;

class MatchingService
{

    public function foundMatching(
        $id,
        JobRepository $jobRepository,
        CandidateRepository $candidateRepository,
    ): array
    {
        $count = 0;
        $offersArray = [];
        #get all job offer
        $offers = $jobRepository->findAll();


        #get the candidate's skills
        $candidate = $candidateRepository->find($id);
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

