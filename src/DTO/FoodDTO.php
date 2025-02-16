<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FoodDTO
{
    #[Assert\NotBlank(message: 'Name cannot be empty')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'Name must be at least 2 characters long')]
    public string $name;

    #[Assert\Choice(choices: ['fruit', 'vegetable'], message: "Type must be 'fruit' or 'vegetable'")]
    public string $type;

    #[Assert\Positive(message: 'Quantity must be greater than zero')]
    public int $quantity;

    #[Assert\Choice(choices: ['g', 'kg'], message: "Unit must be 'g' or 'kg'")]
    public string $unit;
}
