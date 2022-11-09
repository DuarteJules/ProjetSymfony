<?php

namespace App\Service;

use App\Entity\Candidate;

class MatchingService
{
    public function matching(array $offers, Candidate $candidate): array
    {

        $match = ['you matche with a great job',
            'candidate?',
            'allons-y',
            'test'
        ];
$resultOffers=[];
$resultOffers[]=$offers[0];
        return $resultOffers;
    }
}