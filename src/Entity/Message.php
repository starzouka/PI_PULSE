<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'messages',
    indexes: [
        new ORM\Index(name: 'idx_messages_sender', columns: ['sender_user_id']),
        new ORM\Index(name: 'idx_messages_receiver', columns: ['receiver_user_id']),
        new ORM\Index(name: 'idx_messages_created', columns: ['created_at']),
    ]
)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'message_id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'sender_user_id',
        referencedColumnName: 'user_id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    private User $sender;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'receiver_user_id',
        referencedColumnName: 'user_id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    private User $receiver;

    #[ORM\Column(name: 'body_text', type: Types::TEXT)]
    private string $bodyText;

    #[ORM\Column(
        name: 'created_at',
        type: Types::DATETIME_IMMUTABLE,
        columnDefinition: 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP'
    )]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'is_read', type: Types::BOOLEAN, options: ['default' => 0])]
    private bool $isRead = false;

    #[ORM\Column(name: 'read_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $readAt = null;

    #[ORM\Column(name: 'is_deleted_by_sender', type: Types::BOOLEAN, options: ['default' => 0])]
    private bool $isDeletedBySender = false;

    #[ORM\Column(name: 'is_deleted_by_receiver', type: Types::BOOLEAN, options: ['default' => 0])]
    private bool $isDeletedByReceiver = false;

    public function getId(): ?int
    {
        return $this->id;
    }
}
