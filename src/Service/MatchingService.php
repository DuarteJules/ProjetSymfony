<?php

namespace App\Service;

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
        int $id,
    ): array
    {
        $jobsArray = [];
        #get all job offer
        $jobs = $this->jobRepository->findAll();

        #get the candidate's skills
        $candidate = $this->candidateRepository->find($id);
        $candidateSkills = $candidate->getSkills();

        foreach ($jobs as $job) {
            $commonSkill=0;
            {
                foreach ($job->getSkills() as $skillJob) {

                    foreach ($candidateSkills as $skillC) {

                        if ($skillJob != $skillC) {

                        }elseif ($commonSkill >= 2){
                            $jobsArray[] = $job;
                        }else{
                            $commonSkill = $commonSkill+1;
                        }
                    }
                }
            }
        }
        return $jobsArray;
    }
}
        #compare offers.skill and candidate.skills
        #if candidate.skills !== offer.skills[i] => {return 'osef'}
        #else{return $offers[i]

