<?php

namespace App\Tests\Unit;

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
use App\Factory\BlockFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BlockFactoryTest extends TestCase
{
    private BlockFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new BlockFactory();
    }

    public function testCreate_TextType_ReturnsBlockText(): void
    {
        $block = $this->factory->create(BlockType::TEXT);
        $this->assertInstanceOf(BlockText::class, $block);
        $this->assertSame(BlockType::TEXT, $block->getType());
    }

    public function testCreate_ImageType_ReturnsBlockImage(): void
    {
        $block = $this->factory->create(BlockType::IMAGE);
        $this->assertInstanceOf(BlockImage::class, $block);
    }

    public function testCreate_SliderType_ReturnsBlockSlider(): void
    {
        $block = $this->factory->create(BlockType::SLIDER);
        $this->assertInstanceOf(BlockSlider::class, $block);
    }

    public function testCreate_VideoType_ReturnsBlockVideo(): void
    {
        $block = $this->factory->create(BlockType::VIDEO);
        $this->assertInstanceOf(BlockVideo::class, $block);
    }

    public function testCreate_CtaType_ReturnsBlockCta(): void
    {
        $block = $this->factory->create(BlockType::CTA);
        $this->assertInstanceOf(BlockCta::class, $block);
    }

    public function testCreate_DividerType_ReturnsBlockDivider(): void
    {
        $block = $this->factory->create(BlockType::DIVIDER);
        $this->assertInstanceOf(BlockDivider::class, $block);
    }

    public function testCreate_StatsType_ReturnsBlockStats(): void
    {
        $block = $this->factory->create(BlockType::STATS);
        $this->assertInstanceOf(BlockStats::class, $block);
        $this->assertSame(BlockType::STATS, $block->getType());
    }

    public function testCreate_CardsType_ReturnsBlockCards(): void
    {
        $block = $this->factory->create(BlockType::CARDS);
        $this->assertInstanceOf(BlockCards::class, $block);
    }

    public function testCreate_TimelineType_ReturnsBlockTimeline(): void
    {
        $block = $this->factory->create(BlockType::TIMELINE);
        $this->assertInstanceOf(BlockTimeline::class, $block);
    }

    public function testCreate_ContactType_ReturnsBlockContact(): void
    {
        $block = $this->factory->create(BlockType::CONTACT);
        $this->assertInstanceOf(BlockContact::class, $block);
    }

    public function testCreate_FaqType_ReturnsBlockFaq(): void
    {
        $block = $this->factory->create(BlockType::FAQ);
        $this->assertInstanceOf(BlockFaq::class, $block);
    }

    public function testCreate_GalleryType_ReturnsBlockGallery(): void
    {
        $block = $this->factory->create(BlockType::GALLERY);
        $this->assertInstanceOf(BlockGallery::class, $block);
    }

    public function testCreateFromString_ValidType_ReturnsCorrectBlock(): void
    {
        $block = $this->factory->createFromString('text');
        $this->assertInstanceOf(BlockText::class, $block);
    }

    public function testCreateFromString_InvalidType_ThrowsValueError(): void
    {
        $this->expectException(\ValueError::class);
        $this->factory->createFromString('invalid_type');
    }

    #[DataProvider('allBlockTypesProvider')]
    public function testCreate_AllTypes_ReturnCorrectType(BlockType $type): void
    {
        $block = $this->factory->create($type);
        $this->assertSame($type, $block->getType());
    }

    public static function allBlockTypesProvider(): array
    {
        return array_map(fn(BlockType $t) => [$t], BlockType::cases());
    }
}
