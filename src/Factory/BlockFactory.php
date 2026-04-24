<?php

namespace App\Factory;

use App\Entity\Block;
use App\Entity\BlockCta;
use App\Entity\BlockDivider;
use App\Entity\BlockImage;
use App\Entity\BlockSlider;
use App\Entity\BlockText;
use App\Entity\BlockVideo;
use App\Enum\BlockType;

class BlockFactory
{
    public function create(BlockType $type): Block
    {
        return match($type) {
            BlockType::TEXT    => new BlockText(),
            BlockType::IMAGE   => new BlockImage(),
            BlockType::SLIDER  => new BlockSlider(),
            BlockType::VIDEO   => new BlockVideo(),
            BlockType::CTA     => new BlockCta(),
            BlockType::DIVIDER => new BlockDivider(),
        };
    }

    public function createFromString(string $type): Block
    {
        return $this->create(BlockType::from($type));
    }
}
