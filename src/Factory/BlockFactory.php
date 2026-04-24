<?php

namespace App\Factory;

use App\Entity\Block;
use App\Entity\BlockCards;
use App\Entity\BlockContact;
use App\Entity\BlockCta;
use App\Entity\BlockDivider;
use App\Entity\BlockFaq;
use App\Entity\BlockGallery;
use App\Entity\BlockImage;
use App\Entity\BlockSlider;
use App\Entity\BlockStats;
use App\Entity\BlockText;
use App\Entity\BlockTimeline;
use App\Entity\BlockVideo;
use App\Enum\BlockType;

class BlockFactory
{
    public function create(BlockType $type): Block
    {
        return match($type) {
            BlockType::TEXT         => new BlockText(),
            BlockType::IMAGE        => new BlockImage(),
            BlockType::SLIDER       => new BlockSlider(),
            BlockType::VIDEO        => new BlockVideo(),
            BlockType::CTA          => new BlockCta(),
            BlockType::DIVIDER      => new BlockDivider(),
            BlockType::STATS        => new BlockStats(),
            BlockType::CARDS        => new BlockCards(),
            BlockType::TIMELINE     => new BlockTimeline(),
            BlockType::CONTACT      => new BlockContact(),
            BlockType::FAQ          => new BlockFaq(),
            BlockType::GALLERY      => new BlockGallery(),
        };
    }

    public function createFromString(string $type): Block
    {
        return $this->create(BlockType::from($type));
    }
}
