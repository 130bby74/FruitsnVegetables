<?php

namespace App\Command;

use App\Entity\Food;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:load-food-fixtures')]
class LoadFoodFixturesCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this->setDescription('Load food fixtures');
        $this->setHelp('This command loads food fixtures from a JSON file');
        $this->addArgument('json', InputArgument::OPTIONAL, 'JSON file', 'request.json');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $jsonFile = __DIR__.'/../../'.$input->getArgument('json');
        if (!file_exists($jsonFile)) {
            $output->writeln('<error>JSON file not found!</error>');

            return Command::FAILURE;
        }

        $data = json_decode(file_get_contents($jsonFile), true);

        foreach ($data as $item) {
            $food = new Food();
            $food->setName($item['name'])
                ->setType($item['type'])
                ->setQuantity($this->convertToGrams($item['quantity'], $item['unit']));

            $this->entityManager->persist($food);
        }

        $this->entityManager->flush();
        $output->writeln('<info>Food fixtures loaded successfully!</info>');

        return Command::SUCCESS;
    }

    private function convertToGrams(int $quantity, string $unit): int
    {
        return 'kg' === $unit ? $quantity * 1000 : $quantity;
    }
}
