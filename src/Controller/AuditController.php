<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\AuditReport;
use App\Repository\ListingRepository;
use App\Service\ListingAuditService;
use Doctrine\ORM\EntityManagerInterface;

final class AuditController extends AbstractController
{
    public function __construct(
        private ListingRepository $repository,
        private EntityManagerInterface $em,
        private ListingAuditService $auditService,
    ) {}

    #[Route('/listings/{id}/audit', methods:['POST'])]
    public function create(int $id): JsonResponse
    {
        $listing = $this->repository->find($id);

        if (!$listing) {
            return new JsonResponse(['error' => 'Listing not found'], 404);
        }

        $report = $this->auditService->audit($listing);

        $this->em->persist($report);
        $this->em->flush();

        return new JsonResponse([
            'score'  => $report->getScore(),
            'issues' => $report->getIssues(),
        ], 201);
    }

    #[Route('/listings/{id}/audit', methods:['GET'])]
    public function show(int $id): JsonResponse
    {
        $listing = $this->repository->find($id);

        if (!$listing) {
            return new JsonResponse(['error' => 'Listing not found'], 404);
        }

        $report = $listing->getAuditReports()->last();

        if (!$report) {
            return new JsonResponse(['error' => 'No audit found for this listing'], 404);
        }

        return new JsonResponse([
            'score'      => $report->getScore(),
            'issues'     => $report->getIssues(),
            'created_at' => $report->getCreatedAt()->format('c'),
        ]);
    }
}
