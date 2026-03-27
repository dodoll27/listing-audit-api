<?php

namespace App\Controller;

use App\Repository\ListingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Listing;
use App\Enum\PropertyType;

class ListingController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ListingRepository $repository
    ) {}

    #[Route('/listings', methods:['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $listing = new Listing();
        $listing->setTitle($data['title']);
        $listing->setCity($data['city']);
        $listing->setSurface($data['surface']);
        $listing->setType(PropertyType::from($data['type']));
        $listing->setPrice($data['price']);
        $listing->setPhotosCount($data['photos_count']);
        $listing->setDescription($data['description'] ?? null);

        $this->em->persist($listing);
        $this->em->flush();

        return new JsonResponse(['id' => $listing->getId()], 201);

    }

    #[Route('/listings/{id}', methods:['GET'])]
    public function show(int $id): JsonResponse 
    {
        $listing = $this->repository->find($id);

        if (!$listing){
            return new JsonResponse(['error' => 'Listing not found'], 404 );
        }

        return new JsonResponse([
            'id'=>$listing->getId(),
            'title'=>$listing->getTitle(),
            'city'=>$listing->getCity(),
            'surface'=>$listing->getSurface(),
            'type'=>$listing->getType()->value,
            'price'=>$listing->getPrice(),
            'photos_count'=>$listing->getPhotosCount(),
            'description'=>$listing->getDescription(),
            'created_at'=>$listing->getCreatedAt()->format('c')
        ]);
    }
}