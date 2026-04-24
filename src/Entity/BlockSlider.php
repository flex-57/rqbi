<?php

namespace App\Entity;

use App\Enum\BlockType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class BlockSlider extends Block
{
    public function getType(): BlockType
    {
        return BlockType::SLIDER;
    }

    /** @return array<array{url: string, alt: string, caption?: string}> */
    public function getSlides(): array { return $this->content['slides'] ?? []; }
    public function setSlides(array $slides): static { $this->content['slides'] = $slides; return $this; }

    public function getAutoplay(): bool { return $this->content['autoplay'] ?? true; }
    public function setAutoplay(bool $autoplay): static { $this->content['autoplay'] = $autoplay; return $this; }

    public function getInterval(): int { return $this->content['interval'] ?? 4000; }
    public function setInterval(int $interval): static { $this->content['interval'] = $interval; return $this; }
}
