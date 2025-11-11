<?php

namespace App\Domain\Post\ValueObjects;


final class Body
{
    private string $body;

    public function __construct(string $body)
    {
        $this->body = $body;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}