<?php

namespace App\Entity;

use App\Enum\BlockType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class BlockImage extends Block
{
    public function getType(): BlockType
    {
        return BlockType::IMAGE;
    }

    public function getUrl(): string { return $this->content['url'] ?? ''; }
    public function setUrl(string $url): static { $this->content['url'] = $url; return $this; }

    public function getAlt(): string { return $this->content['alt'] ?? ''; }
    public function setAlt(string $alt): static { $this->content['alt'] = $alt; return $this; }

    public function getCaption(): ?string { return $this->content['caption'] ?? null; }
    public function setCaption(?string $caption): static { $this->content['caption'] = $caption; return $this; }
}
