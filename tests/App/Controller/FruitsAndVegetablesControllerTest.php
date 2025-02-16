<?php

namespace App\Tests\App\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FruitsAndVegetablesControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testListFoods(): void
    {
        $this->client->request('GET', '/api/foods/fruit');

        self::assertResponseIsSuccessful();
        $expectedJson = 'name":"Apples","type":"fruit","quantity":20000,"unit":"g"';
        $this->assertStringContainsString($expectedJson, $this->client->getResponse()->getContent());
    }

    public function testAddValidFood(): void
    {
        $this->client->request('POST', '/api/foods/fruit/add', [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['name' => 'Banana', 'quantity' => 3, 'unit' => 'kg']));

        self::assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Fruit added successfully']),
            $this->client->getResponse()->getContent()
        );
    }

    public function testAddInvalidType(): void
    {
        $this->client->request('POST', '/api/foods/meat/add', [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode(['name' => 'Steak', 'quantity' => 2, 'unit' => 'kg']));

        self::assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => "Type must be 'fruit' or 'vegetable'"]),
            $this->client->getResponse()->getContent()
        );
    }

    public function testAddInvalidFoodValidationError(): void
    {
        $this->client->request('POST', '/api/foods/fruit/add', [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode(['name' => '', 'quantity' => -5, 'unit' => 'kg']));

        self::assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['errors' => ['Name cannot be empty', 'Name must be at least 2 characters long', 'Quantity must be greater than zero']]),
            $this->client->getResponse()->getContent()
        );
    }

    public function testAddInvalidFoodSerializationError(): void
    {
        $this->client->request('POST', '/api/foods/fruit/add', [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode(['name' => '', 'quantity' => '-5', 'unit' => 'kg'])
        );

        self::assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => "Parameters don't have the required type"]),
            $this->client->getResponse()->getContent()
        );
    }
}
