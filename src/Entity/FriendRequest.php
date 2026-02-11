<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'friend_requests',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uq_friend_req_pair', columns: ['from_user_id', 'to_user_id']),
    ],
    indexes: [
        new ORM\Index(name: 'idx_friend_req_to', columns: ['to_user_id']),
        new ORM\Index(name: 'idx_friend_req_from', columns: ['from_user_id']),
    ]
)]
class FriendRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'request_id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'from_user_id',
        referencedColumnName: 'user_id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    private User $fromUser;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'to_user_id', referencedColumnName: 'user_id', nullable: false, onDelete: 'CASCADE')]
    private User $toUser;

    #[ORM\Column(
        name: 'status',
        type: Types::STRING,
        length: 10,
        columnDefinition: "ENUM('PENDING','ACCEPTED','REFUSED','CANCELLED') NOT NULL DEFAULT 'PENDING'"
    )]
    private string $status = 'PENDING';

    #[ORM\Column(name: 'request_message', type: Types::STRING, length: 255, nullable: true)]
    private ?string $requestMessage = null;

    #[ORM\Column(
        name: 'created_at',
        type: Types::DATETIME_IMMUTABLE,
        columnDefinition: 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP'
    )]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'responded_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $respondedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
