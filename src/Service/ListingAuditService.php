<?php

namespace App\Service;

use App\Entity\AuditReport;
use App\Entity\Listing;

class ListingAuditService
{
    public function audit(Listing $listing): AuditReport
    {
        $issues = [];
        $score = 100;

        $description = $listing->getDescription();

        if (!$description){
            $issues[]=[
                'field'=>'description',
                'severity'=>'critical',
                'message'=>'Description is missing'
            ];
            $score -= 30;
        } elseif (strlen($description) < 100) {
            $issues[] = [
                'field'=>'description',
                'severity'=>'warning',
                'message'=>'Description is too short'
            ];
            $score -= 15;
        }

        $photosCount = $listing->getPhotosCount();

        if (!$photosCount){
            $issues[]=[
                'field'=>'photos_count',
                'severity'=>'critical',
                'message'=>'Photos are missing',
            ];
            $score -= 30;
        } elseif ($photosCount < 5) {
            $issues[]=[
                'field'=>'photos_count',
                'severity'=>'warning',
                'message'=>'Min photos required 5'
            ];
            $score -= 15;
        }

        $title = $listing->getTitle();

        if(!$title){
            $issues[]=[
                'field'=>'title',
                'severity'=>'critical',
                'message'=>'Missing title'
            ];
            $score -= 30;
        } elseif (strlen($title) < 15){
            $issues[]=[
                'field'=>'title',
                'severity'=>'warning',
                'message'=>'Title is too short',
            ];
            $score -=15;
        }

        $price = $listing->getPrice();

        if(!$price){
            $issues[]=[
                'field'=>'price',
                'severity'=>'critical',
                'message'=>'Missing price'
            ];
            $score -= 30;
        } elseif ($price < 1000) {
            $issues[] = [
                'field'    => 'price',
                'severity' => 'warning',
                'message'  => 'Price seems suspiciously low',
            ];
            $score -= 15;
        }


        $report = new AuditReport();
        $report->setScore($score);
        $report->setIssues($issues);
        $report->setCreatedAt(new \DateTimeImmutable());
        $report->setListing($listing);

        return $report;
    }
}