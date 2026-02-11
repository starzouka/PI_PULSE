<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'team_join_requests',
    indexes: [
        new ORM\Index(name: 'idx_team_join_req_team', columns: ['team_id']),
        new ORM\Index(name: 'idx_team_join_req_user', columns: ['user_id']),
        new ORM\Index(name: 'idx_team_join_req_status', columns: ['status']),
    ]
)]
class TeamJoinRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'request_id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'team_id', referencedColumnName: 'team_id', nullable: false, onDelete: 'CASCADE')]
    private Team $team;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(
        name: 'status',
        type: Types::STRING,
        length: 10,
        columnDefinition: "ENUM('PENDING','ACCEPTED','REFUSED','CANCELLED') NOT NULL DEFAULT 'PENDING'"
    )]
    private string $status = 'PENDING';

    #[ORM\Column(name: 'note', type: Types::STRING, length: 255, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(
        name: 'created_at',
        type: Types::DATETIME_IMMUTABLE,
        columnDefinition: 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP'
    )]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'responded_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $respondedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'responded_by_captain_id',
        referencedColumnName: 'user_id',
        nullable: true,
        onDelete: 'SET NULL'
    )]
    private ?User $respondedByCaptain = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
