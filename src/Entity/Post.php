<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'posts',
    indexes: [
        new ORM\Index(name: 'idx_posts_author', columns: ['author_user_id']),
        new ORM\Index(name: 'idx_posts_created', columns: ['created_at']),
    ]
)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'post_id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'author_user_id',
        referencedColumnName: 'user_id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    private User $author;

    #[ORM\Column(name: 'content_text', type: Types::TEXT, nullable: true)]
    private ?string $contentText = null;

    #[ORM\Column(
        name: 'visibility',
        type: Types::STRING,
        length: 20,
        columnDefinition: "ENUM('PUBLIC','FRIENDS','TEAM_ONLY') NOT NULL DEFAULT 'PUBLIC'"
    )]
    private string $visibility = 'PUBLIC';

    #[ORM\Column(name: 'is_deleted', type: Types::BOOLEAN, options: ['default' => 0])]
    private bool $isDeleted = false;

    #[ORM\Column(name: 'deleted_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\Column(
        name: 'created_at',
        type: Types::DATETIME_IMMUTABLE,
        columnDefinition: 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP'
    )]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(
        name: 'updated_at',
        type: Types::DATETIME_IMMUTABLE,
        columnDefinition: 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
    )]
    private \DateTimeImmutable $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }
}
