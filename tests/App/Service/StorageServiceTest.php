<?php

namespace App\Tests\App\Service;

use App\DTO\FoodDTO;
use App\Entity\Food;
use App\Service\StorageService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StorageServiceTest extends TestCase
{
    private StorageService $storageService;
    private MockObject $entityManager;
    private MockObject $validator;
    private MockObject $foodRepository;

    protected function setUp(): void
    {
        $this->entityManager  = $this->createMock(EntityManagerInterface::class);
        $this->validator      = $this->createMock(ValidatorInterface::class);
        $this->foodRepository = $this->createMock(EntityRepository::class);
        $this->entityManager->method('getRepository')->willReturn($this->foodRepository);

        $this->storageService = new StorageService($this->entityManager, $this->validator);
    }

    public function testAddValidFood(): void
    {
        $foodDTO           = new FoodDTO();
        $foodDTO->name     = 'Apple';
        $foodDTO->quantity = 5;
        $foodDTO->unit     = 'kg';

        $this->validator->method('validate')->willReturn(new ConstraintViolationList());
        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');
        $this->storageService->add('fruit', $foodDTO);
    }

    public function testInvalidTypeFood(): void
    {
        $foodDTO           = new FoodDTO();
        $foodDTO->name     = 'Apple';
        $foodDTO->quantity = 5;
        $foodDTO->unit     = 'kg';

        $this->expectException(\InvalidArgumentException::class);
        $this->storageService->add('fruits', $foodDTO);
    }

    public function testAddInvalidFoodThrowsValidationException(): void
    {
        $foodDTO           = new FoodDTO();
        $foodDTO->name     = '';
        $foodDTO->quantity = -1;
        $foodDTO->unit     = 'kg';

        $violations = new ConstraintViolationList([
            new \Symfony\Component\Validator\ConstraintViolation('Name cannot be empty', null, [], '', 'name', ''),
            new \Symfony\Component\Validator\ConstraintViolation('Quantity must be positive', null, [], '', 'quantity', -1),
        ]);

        $this->validator->method('validate')->willReturn($violations);
        $this->expectException(\Symfony\Component\Validator\Exception\ValidationFailedException::class);
        $this->storageService->add('fruit', $foodDTO);
    }

    public function testRemoveFood(): void
    {
        $food = new Food();
        $food->setName('Carrot')->setType('vegetable')->setQuantity(100);
        $this->foodRepository->method('find')->willReturn($food);
        $this->entityManager->expects($this->once())->method('remove')->with($food);
        $this->entityManager->expects($this->once())->method('flush');
        $this->storageService->remove(1);
    }

    public function testListReturnsFoods(): void
    {
        $food = new Food();
        $food->setName('Apple')->setType('fruit')->setQuantity(1000);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query        = $this->createMock(Query::class);
        $this->foodRepository->method('createQueryBuilder')->willReturn($queryBuilder);
        $queryBuilder->method('getQuery')->willReturn($query);
        $query->method('getResult')->willReturn([$food]);
        $result = $this->storageService->list('fruit', ['name' => 'Apple']);

        $this->assertCount(1, $result);
        $this->assertEquals('Apple', $result[0]['name']);
        $this->assertEquals('fruit', $result[0]['type']);
        $this->assertEquals(1000, $result[0]['quantity']);
        $this->assertEquals('g', $result[0]['unit']);
    }
}
