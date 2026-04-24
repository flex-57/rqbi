<?php

namespace App\Entity;

use App\Enum\BlockType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class BlockFaq extends Block
{
    public function getType(): BlockType
    {
        return BlockType::FAQ;
    }
}
