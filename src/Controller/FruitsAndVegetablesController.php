<?php

namespace App\Controller;

use App\DTO\FoodDTO;
use App\Service\StorageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class FruitsAndVegetablesController extends AbstractController
{
    private StorageService $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    #[Route('/api/foods/{type}', methods: ['GET'])]
    public function list(string $type, Request $request): JsonResponse
    {
        $filters = $request->query->all();
        $data    = $this->storageService->list($type, $filters);

        return $this->json($data);
    }

    #[Route('/api/foods/{type}/add', methods: ['POST'])]
    public function add(string $type, Request $request, SerializerInterface $serializer): JsonResponse
    {
        if (!in_array($type, ['fruit', 'vegetable'])) {
            return $this->json(['error' => "Type must be 'fruit' or 'vegetable'"], 400);
        }
        try {
            $foodDTO = $serializer->deserialize($request->getContent(), FoodDTO::class, 'json');
        } catch (NotNormalizableValueException $e) {
            return $this->json(['error' => "Parameters don't have the required type"], 400);
        }

        try {
            $this->storageService->add($type, $foodDTO);
        } catch (ValidationFailedException $e) {
            $errorMessages = [];
            foreach ($e->getViolations() as $violation) {
                $errorMessages[] = $violation->getMessage();
            }

            return $this->json(['errors' => $errorMessages], 400);
        }

        return $this->json(['message' => ucfirst($type).' added successfully']);
    }
}
