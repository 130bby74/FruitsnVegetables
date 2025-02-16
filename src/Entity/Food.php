<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'food')]
class Food
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Name cannot be empty')]
    private string $name;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Choice(choices: ['fruit', 'vegetable'], message: "Type must be 'fruit' or 'vegetable'")]
    private string $type; // 'fruit' or 'vegetable'

    #[ORM\Column(type: 'integer')]
    #[Assert\Positive(message: 'Quantity must be greater than zero')]
    private int $quantity; // Stored in grams

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        if (!in_array($type, ['fruit', 'vegetable'])) {
            throw new \InvalidArgumentException("Type must be 'fruit' or 'vegetable'");
        }
        $this->type = $type;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
