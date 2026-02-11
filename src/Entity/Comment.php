<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'comments',
    indexes: [
        new ORM\Index(name: 'idx_comments_post', columns: ['post_id']),
        new ORM\Index(name: 'idx_comments_author', columns: ['author_user_id']),
        new ORM\Index(name: 'idx_comments_parent', columns: ['parent_comment_id']),
    ]
)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'comment_id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'post_id', referencedColumnName: 'post_id', nullable: false, onDelete: 'CASCADE')]
    private Post $post;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'author_user_id',
        referencedColumnName: 'user_id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    private User $author;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'parent_comment_id',
        referencedColumnName: 'comment_id',
        nullable: true,
        onDelete: 'SET NULL'
    )]
    private ?Comment $parentComment = null;

    #[ORM\Column(name: 'content_text', type: Types::TEXT)]
    private string $contentText;

    #[ORM\Column(name: 'is_deleted', type: Types::BOOLEAN, options: ['default' => 0])]
    private bool $isDeleted = false;

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
