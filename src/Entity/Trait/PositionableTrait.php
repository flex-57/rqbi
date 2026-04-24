<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait PositionableTrait
{
    #[ORM\Column(type: 'integer')]
    private int $position = 0;

    public function getPosition(): int { return $this->position; }
    public function setPosition(int $position): static { $this->position = $position; return $this; }
}
