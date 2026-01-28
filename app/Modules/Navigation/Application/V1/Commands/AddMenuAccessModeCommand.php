<?php

namespace App\Modules\Navigation\Application\V1\Commands;

final class AddMenuAccessModeCommand
{
    public function __construct(
        public readonly string $menuId,
        public readonly array $accessModes,
    ) {}
}
