<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'tournament_teams',
    indexes: [
        new ORM\Index(name: 'idx_tteam_team', columns: ['team_id']),
        new ORM\Index(name: 'idx_tteam_status', columns: ['status']),
        new ORM\Index(name: 'idx_tteam_decider', columns: ['decided_by_user_id']),
    ]
)]
class TournamentTeam
{
    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'tournament_id',
        referencedColumnName: 'tournament_id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    private Tournament $tournament;

    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'team_id', referencedColumnName: 'team_id', nullable: false, onDelete: 'CASCADE')]
    private Team $team;

    #[ORM\Column(
        name: 'status',
        type: Types::STRING,
        length: 10,
        columnDefinition: "ENUM('PENDING','ACCEPTED','REFUSED','CANCELLED') NOT NULL DEFAULT 'PENDING'"
    )]
    private string $status = 'PENDING';

    #[ORM\Column(name: 'seed', type: Types::INTEGER, nullable: true, options: ['unsigned' => true])]
    private ?int $seed = null;

    #[ORM\Column(
        name: 'registered_at',
        type: Types::DATETIME_IMMUTABLE,
        columnDefinition: 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP'
    )]
    private \DateTimeImmutable $registeredAt;

    #[ORM\Column(name: 'decided_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $decidedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'decided_by_user_id',
        referencedColumnName: 'user_id',
        nullable: true,
        onDelete: 'SET NULL'
    )]
    private ?User $decidedByUser = null;

    #[ORM\Column(name: 'checked_in', type: Types::BOOLEAN, options: ['default' => 0])]
    private bool $checkedIn = false;

    #[ORM\Column(name: 'checkin_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $checkinAt = null;
}
