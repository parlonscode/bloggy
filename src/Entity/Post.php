<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PostRepository;
use App\Entity\Traits\Timestampable;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\Table(name: '`posts`')]
#[ORM\UniqueConstraint(
    name: 'unique_slug_for_publish_date',
    columns: ['published_at', 'slug']
)]
#[ORM\HasLifecycleCallbacks]
class Post
{
    use Timestampable;

    public const NUM_ITEMS_PER_PAGE = 2;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $body = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $publishedAt;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getPathParams(): array
    {
        return [
            'date' => $this->getPublishedAt()->format('Y-m-d'),
            'slug' => $this->getSlug()
        ];
    }
}
