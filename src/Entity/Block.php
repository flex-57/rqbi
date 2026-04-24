<?php

namespace App\Entity;

use App\Entity\Trait\PositionableTrait;
use App\Entity\Trait\TimestampableTrait;
use App\Enum\BlockType;
use App\Repository\BlockRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: BlockRepository::class)]
#[ORM\Table(name: 'block')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'text'    => BlockText::class,
    'image'   => BlockImage::class,
    'slider'  => BlockSlider::class,
    'video'   => BlockVideo::class,
    'cta'     => BlockCta::class,
    'divider' => BlockDivider::class,
])]
#[ORM\HasLifecycleCallbacks]
abstract class Block
{
    use TimestampableTrait;
    use PositionableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['block:read', 'page:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Page::class, inversedBy: 'blocks')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Page $page;

    #[ORM\Column(type: 'json')]
    #[Groups(['block:read', 'page:read'])]
    protected array $content = [];

    #[ORM\Column(type: 'boolean')]
    #[Groups(['block:read', 'page:read'])]
    private bool $visible = true;

    public function getId(): ?int { return $this->id; }

    public function getPage(): Page { return $this->page; }
    public function setPage(Page $page): static { $this->page = $page; return $this; }

    public function getContent(): array { return $this->content; }
    public function setContent(array $content): static { $this->content = $content; return $this; }

    public function isVisible(): bool { return $this->visible; }
    public function setVisible(bool $visible): static { $this->visible = $visible; return $this; }

    #[Groups(['block:read', 'page:read'])]
    abstract public function getType(): BlockType;
}
