<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Block;
use App\Entity\BlockImage;
use App\Entity\BlockText;
use App\Entity\BlockVideo;
use App\Entity\Enums\BlockTypeEnum;
use App\Entity\Enums\VideoFormatEnum;
use App\Factory\BlockFactory;
use App\Factory\SingleBlockFactory\SingleBlockFactoryInterface;
use PHPUnit\Framework\TestCase;

class BlockFactoryTest extends TestCase
{
    private BlockFactory $factory;

    protected function setUp(): void
    {
        // 1. Stub Text Factory
        $textFactory = new class implements SingleBlockFactoryInterface {
            public static function getSupportedType(): BlockTypeEnum
            {
                return BlockTypeEnum::TEXT;
            }

            public function create(array $data): Block
            {
                return (new BlockText())->setContent($data['content'] ?? '');
            }
        };

        // 2. Stub Image Factory
        $imageFactory = new class implements SingleBlockFactoryInterface {
            public static function getSupportedType(): BlockTypeEnum
            {
                return BlockTypeEnum::IMAGE;
            }

            public function create(array $data): Block
            {
                return (new BlockImage())
                    ->setUrl($data['url'] ?? '')
                    ->setAlt($data['alt'] ?? '');
            }
        };

        // 3. Stub Video Factory
        $videoFactory = new class implements SingleBlockFactoryInterface {
            public static function getSupportedType(): BlockTypeEnum
            {
                return BlockTypeEnum::VIDEO;
            }

            public function create(array $data): Block
            {
                return (new BlockVideo())
                    ->setUrl($data['url'] ?? '')
                    ->setFormat($data['format'] ?? VideoFormatEnum::MP4)
                    ->setIsAutoplay($data['isAutoplay'] ?? false);
            }
        };

        // 4. Injecte les stubs
        $this->factory = new BlockFactory([$textFactory, $imageFactory, $videoFactory]);
    }

    public function testCreateTextBlock(): void
    {
        $data = ['content' => 'Hello Text'];
        $block = $this->factory->create(BlockTypeEnum::TEXT, $data);

        /** @var BlockText $block */
        $this->assertInstanceOf(BlockText::class, $block);
        $this->assertSame('Hello Text', $block->getContent());
    }

    public function testCreateImageBlock(): void
    {
        $data = ['url' => 'https://image.jpg', 'alt' => 'Alt text'];
        $block = $this->factory->create(BlockTypeEnum::IMAGE, $data);

        /** @var BlockImage $block */
        $this->assertInstanceOf(BlockImage::class, $block);
        $this->assertSame('https://image.jpg', $block->getUrl());
        $this->assertSame('Alt text', $block->getAlt());
    }

    public function testCreateVideoBlock(): void
    {
        $data = ['url' => 'https://video.mp4', 'format' => VideoFormatEnum::MP4, 'isAutoplay' => true];
        $block = $this->factory->create(BlockTypeEnum::VIDEO, $data);

        /** @var BlockVideo $block */
        $this->assertInstanceOf(BlockVideo::class, $block);
        $this->assertSame('https://video.mp4', $block->getUrl());
        $this->assertTrue($block->isAutoplay());
        $this->assertSame(VideoFormatEnum::MP4, $block->getFormat());
    }

    public function testUnsupportedTypeThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No factory registered for block type slider');

        $this->factory->create(BlockTypeEnum::SLIDER, []);
    }
}
