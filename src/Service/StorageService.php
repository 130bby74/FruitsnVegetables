<?php

namespace App\Service;

use App\DTO\FoodDTO;
use App\Entity\Food;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StorageService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator     = $validator;
    }

    private function convertToGrams(int $quantity, string $unit): int
    {
        return 'kg' === $unit ? $quantity * 1000 : $quantity;
    }

    public function add(string $type, FoodDTO $item): void
    {
        $errors = $this->validator->validate($item);
        if (count($errors) > 0) {
            throw new ValidationFailedException($item, $errors);
        }

        $quantityInGrams = $this->convertToGrams($item->quantity, $item->unit);

        $food = new Food();
        $food->setName($item->name)
            ->setType($type)
            ->setQuantity($quantityInGrams);

        $this->entityManager->persist($food);
        $this->entityManager->flush();
    }

    public function remove(int $id): void
    {
        $food = $this->entityManager->getRepository(Food::class)->find($id);
        if ($food) {
            $this->entityManager->remove($food);
            $this->entityManager->flush();
        }
    }

    public function list(string $type, ?array $filters = []): array
    {
        $queryBuilder = $this->entityManager->getRepository(Food::class)->createQueryBuilder('f');

        if ('all' !== $type) {
            $queryBuilder->andWhere('LOWER(f.type) = LOWER(:type)')->setParameter('type', $type);
        }

        if (isset($filters['name'])) {
            $queryBuilder->andWhere('LOWER(f.name) = LOWER(:name)')
                ->setParameter('name', strtolower($filters['name']));
        }

        $foods = $queryBuilder->getQuery()->getResult();

        return array_map(fn ($food) => [
            'id'       => $food->getId(),
            'name'     => $food->getName(),
            'type'     => $food->getType(),
            'quantity' => $food->getQuantity(),
            'unit'     => 'g',
        ], $foods);
    }
}
