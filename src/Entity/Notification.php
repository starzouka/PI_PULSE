<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'notifications',
    indexes: [
        new ORM\Index(name: 'idx_notifications_user', columns: ['user_id']),
        new ORM\Index(name: 'idx_notifications_read', columns: ['is_read', 'created_at']),
    ]
)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'notification_id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(
        name: 'type',
        type: Types::STRING,
        length: 40,
        columnDefinition: "ENUM('FRIEND_REQUEST','TEAM_INVITE','TEAM_JOIN_RESPONSE','NEW_MESSAGE','TOURNAMENT_REQUEST_STATUS','ORDER_STATUS') NOT NULL"
    )]
    private string $type;

    #[ORM\Column(name: 'ref_table', type: Types::STRING, length: 64, nullable: true)]
    private ?string $refTable = null;

    #[ORM\Column(name: 'ref_id', type: Types::BIGINT, nullable: true, options: ['unsigned' => true])]
    private ?string $refId = null;

    #[ORM\Column(name: 'content', type: Types::STRING, length: 255)]
    private string $content;

    #[ORM\Column(name: 'is_read', type: Types::BOOLEAN, options: ['default' => 0])]
    private bool $isRead = false;

    #[ORM\Column(name: 'read_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $readAt = null;

    #[ORM\Column(
        name: 'created_at',
        type: Types::DATETIME_IMMUTABLE,
        columnDefinition: 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP'
    )]
    private \DateTimeImmutable $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }
}
