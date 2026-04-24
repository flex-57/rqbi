<?php

namespace App\Entity;

use App\Enum\BlockType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class BlockText extends Block
{
    public function getType(): BlockType
    {
        return BlockType::TEXT;
    }

    public function getTitle(): ?string { return $this->content['title'] ?? null; }
    public function setTitle(?string $title): static { $this->content['title'] = $title; return $this; }

    public function getBody(): string { return $this->content['body'] ?? ''; }
    public function setBody(string $body): static { $this->content['body'] = $body; return $this; }
}
