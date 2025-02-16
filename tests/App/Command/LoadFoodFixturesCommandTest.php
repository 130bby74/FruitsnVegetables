<?php

namespace App\Tests\App\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class LoadFoodFixturesCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        self::bootKernel();
        $application   = new Application(self::$kernel);
        $command       = $application->find('app:load-food-fixtures');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Food fixtures loaded successfully', $output);
    }

    public function testExecuteNoFile(): void
    {
        self::bootKernel();
        $application   = new Application(self::$kernel);
        $command       = $application->find('app:load-food-fixtures');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['json' => 'test']);
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('JSON file not found!', $output);
    }
}
