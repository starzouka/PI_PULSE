<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'matches',
    indexes: [
        new ORM\Index(name: 'idx_matches_tournament', columns: ['tournament_id']),
        new ORM\Index(name: 'idx_matches_status', columns: ['status', 'scheduled_at']),
        new ORM\Index(name: 'idx_matches_submitted_by', columns: ['result_submitted_by_user_id']),
    ]
)]
class GameMatch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'match_id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'tournament_id',
        referencedColumnName: 'tournament_id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    private Tournament $tournament;

    #[ORM\Column(name: 'scheduled_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $scheduledAt = null;

    #[ORM\Column(name: 'round_name', type: Types::STRING, length: 80, nullable: true)]
    private ?string $roundName = null;

    #[ORM\Column(name: 'best_of', type: Types::SMALLINT, nullable: true, options: ['unsigned' => true])]
    private ?int $bestOf = null;

    #[ORM\Column(
        name: 'status',
        type: Types::STRING,
        length: 10,
        columnDefinition: "ENUM('SCHEDULED','ONGOING','FINISHED','CANCELLED') NOT NULL DEFAULT 'SCHEDULED'"
    )]
    private string $status = 'SCHEDULED';

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

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'result_submitted_by_user_id',
        referencedColumnName: 'user_id',
        nullable: true,
        onDelete: 'SET NULL'
    )]
    private ?User $resultSubmittedByUser = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
