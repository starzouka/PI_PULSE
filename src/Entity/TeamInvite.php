<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'team_invites',
    indexes: [
        new ORM\Index(name: 'idx_team_invites_team', columns: ['team_id']),
        new ORM\Index(name: 'idx_team_invites_invited', columns: ['invited_user_id']),
        new ORM\Index(name: 'idx_team_invites_by', columns: ['invited_by_user_id']),
        new ORM\Index(name: 'idx_team_invites_status', columns: ['status']),
    ]
)]
class TeamInvite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'invite_id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'team_id', referencedColumnName: 'team_id', nullable: false, onDelete: 'CASCADE')]
    private Team $team;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'invited_user_id',
        referencedColumnName: 'user_id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    private User $invitedUser;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'invited_by_user_id',
        referencedColumnName: 'user_id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    private User $invitedByUser;

    #[ORM\Column(
        name: 'status',
        type: Types::STRING,
        length: 10,
        columnDefinition: "ENUM('PENDING','ACCEPTED','REFUSED','CANCELLED') NOT NULL DEFAULT 'PENDING'"
    )]
    private string $status = 'PENDING';

    #[ORM\Column(name: 'message', type: Types::STRING, length: 255, nullable: true)]
    private ?string $message = null;

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
