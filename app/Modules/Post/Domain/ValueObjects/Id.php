<?php

namespace App\Domain\Post\ValueObjects;

final class Id
{

    public function __construct(private string $id)
    {
        $id = trim($id);
        
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}