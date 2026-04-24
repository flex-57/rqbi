<?php

namespace App\Enum;

enum BlockType: string
{
    case TEXT         = 'text';
    case IMAGE        = 'image';
    case SLIDER       = 'slider';
    case VIDEO        = 'video';
    case CTA          = 'cta';
    case DIVIDER      = 'divider';
    case STATS        = 'stats';
    case CARDS        = 'cards';
    case TIMELINE     = 'timeline';
    case CONTACT      = 'contact';
    case FAQ          = 'faq';
    case GALLERY      = 'gallery';

    public function getClass(): string
    {
        return match($this) {
            self::TEXT         => \App\Entity\BlockText::class,
            self::IMAGE        => \App\Entity\BlockImage::class,
            self::SLIDER       => \App\Entity\BlockSlider::class,
            self::VIDEO        => \App\Entity\BlockVideo::class,
            self::CTA          => \App\Entity\BlockCta::class,
            self::DIVIDER      => \App\Entity\BlockDivider::class,
            self::STATS        => \App\Entity\BlockStats::class,
            self::CARDS        => \App\Entity\BlockCards::class,
            self::TIMELINE     => \App\Entity\BlockTimeline::class,
            self::CONTACT      => \App\Entity\BlockContact::class,
            self::FAQ          => \App\Entity\BlockFaq::class,
            self::GALLERY      => \App\Entity\BlockGallery::class,
        };
    }

    public function getLabel(): string
    {
        return match($this) {
            self::TEXT         => 'Texte',
            self::IMAGE        => 'Image',
            self::SLIDER       => 'Slider',
            self::VIDEO        => 'Vidéo',
            self::CTA          => "Appel à l'action",
            self::DIVIDER      => 'Séparateur',
            self::STATS        => 'Chiffres clés',
            self::CARDS        => 'Cartes',
            self::TIMELINE     => 'Chronologie',
            self::CONTACT      => 'Contact & formulaire',
            self::FAQ          => 'FAQ',
            self::GALLERY      => 'Galerie',
        };
    }
}
