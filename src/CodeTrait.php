<?php

declare(strict_types=1);

namespace Rinsvent\Exception;

trait CodeTrait
{
    public function code(): int
    {
        return array_search($this, self::cases(), true) * 100;
    }
}
