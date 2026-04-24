<?php

namespace App\Entity;

use App\Enum\BlockType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class BlockCta extends Block
{
    public function getType(): BlockType
    {
        return BlockType::CTA;
    }

    public function getTitle(): string { return $this->content['title'] ?? ''; }
    public function setTitle(string $title): static { $this->content['title'] = $title; return $this; }

    public function getSubtitle(): ?string { return $this->content['subtitle'] ?? null; }
    public function setSubtitle(?string $subtitle): static { $this->content['subtitle'] = $subtitle; return $this; }

    public function getButtonLabel(): string { return $this->content['button_label'] ?? ''; }
    public function setButtonLabel(string $label): static { $this->content['button_label'] = $label; return $this; }

    public function getButtonUrl(): string { return $this->content['button_url'] ?? ''; }
    public function setButtonUrl(string $url): static { $this->content['button_url'] = $url; return $this; }

    public function getBackground(): string { return $this->content['background'] ?? 'red'; }
    public function setBackground(string $bg): static { $this->content['background'] = $bg; return $this; }
}
