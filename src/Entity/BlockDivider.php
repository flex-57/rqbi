<?php

namespace App\Entity;

use App\Enum\BlockType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class BlockDivider extends Block
{
    public function getType(): BlockType
    {
        return BlockType::DIVIDER;
    }

    public function getStyle(): string { return $this->content['style'] ?? 'line'; }
    public function setStyle(string $style): static { $this->content['style'] = $style; return $this; }

    public function getLabel(): ?string { return $this->content['label'] ?? null; }
    public function setLabel(?string $label): static { $this->content['label'] = $label; return $this; }
}
