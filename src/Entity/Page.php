<?php

namespace App\Entity;

use App\Entity\Trait\TimestampableTrait;
use App\Repository\PageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Page
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['page:read', 'page:list'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['page:read', 'page:list'])]
    private string $title;

    #[ORM\Column(length: 191, unique: true)]
    #[Groups(['page:read', 'page:list'])]
    private string $slug;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['page:read'])]
    private ?Page $parent = null;

    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent', cascade: ['persist'])]
    #[ORM\OrderBy(['title' => 'ASC'])]
    #[Groups(['page:read'])]
    private Collection $children;

    #[ORM\OneToMany(targetEntity: Block::class, mappedBy: 'page', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    #[Groups(['page:read'])]
    private Collection $blocks;

    #[ORM\Column(type: 'integer')]
    #[Groups(['page:read', 'page:list'])]
    private int $depth = 0;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['page:read', 'page:list'])]
    private bool $published = false;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['page:read', 'page:list'])]
    private ?string $metaTitle = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['page:read', 'page:list'])]
    private ?string $metaDescription = null;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->blocks = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }

    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): static { $this->slug = $slug; return $this; }

    public function getParent(): ?Page { return $this->parent; }
    public function setParent(?Page $parent): static
    {
        $this->parent = $parent;
        $this->depth = $parent ? $parent->getDepth() + 1 : 0;
        return $this;
    }

    public function getChildren(): Collection { return $this->children; }
    public function addChild(Page $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }
        return $this;
    }
    public function removeChild(Page $child): static
    {
        if ($this->children->removeElement($child) && $child->getParent() === $this) {
            $child->setParent(null);
        }
        return $this;
    }

    public function getBlocks(): Collection { return $this->blocks; }
    public function addBlock(Block $block): static
    {
        if (!$this->blocks->contains($block)) {
            $this->blocks->add($block);
            $block->setPage($this);
        }
        return $this;
    }
    public function removeBlock(Block $block): static
    {
        $this->blocks->removeElement($block);
        return $this;
    }

    public function getDepth(): int { return $this->depth; }

    public function isPublished(): bool { return $this->published; }
    public function setPublished(bool $published): static { $this->published = $published; return $this; }

    public function getMetaTitle(): ?string { return $this->metaTitle; }
    public function setMetaTitle(?string $metaTitle): static { $this->metaTitle = $metaTitle; return $this; }

    public function getMetaDescription(): ?string { return $this->metaDescription; }
    public function setMetaDescription(?string $metaDescription): static { $this->metaDescription = $metaDescription; return $this; }
}
