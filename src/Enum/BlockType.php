<?php

namespace App\Enum;

enum BlockType: string
{
    case TEXT = 'text';
    case IMAGE = 'image';
    case SLIDER = 'slider';
    case VIDEO = 'video';
    case CTA = 'cta';
    case DIVIDER = 'divider';

    public function getClass(): string
    {
        return match($this) {
            self::TEXT    => \App\Entity\BlockText::class,
            self::IMAGE   => \App\Entity\BlockImage::class,
            self::SLIDER  => \App\Entity\BlockSlider::class,
            self::VIDEO   => \App\Entity\BlockVideo::class,
            self::CTA     => \App\Entity\BlockCta::class,
            self::DIVIDER => \App\Entity\BlockDivider::class,
        };
    }

    public function getLabel(): string
    {
        return match($this) {
            self::TEXT    => 'Texte',
            self::IMAGE   => 'Image',
            self::SLIDER  => 'Slider',
            self::VIDEO   => 'Vidéo',
            self::CTA     => 'Appel à l\'action',
            self::DIVIDER => 'Séparateur',
        };
    }
}
