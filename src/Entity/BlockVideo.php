<?php

namespace App\Entity;

use App\Enum\BlockType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class BlockVideo extends Block
{
    public function getType(): BlockType
    {
        return BlockType::VIDEO;
    }

    public function getUrl(): string { return $this->content['url'] ?? ''; }
    public function setUrl(string $url): static { $this->content['url'] = $url; return $this; }

    public function getTitle(): ?string { return $this->content['title'] ?? null; }
    public function setTitle(?string $title): static { $this->content['title'] = $title; return $this; }

    public function getProvider(): string { return $this->content['provider'] ?? 'youtube'; }
    public function setProvider(string $provider): static { $this->content['provider'] = $provider; return $this; }
}
