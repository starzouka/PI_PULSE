<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'tournaments',
    indexes: [
        new ORM\Index(name: 'idx_tournaments_organizer', columns: ['organizer_user_id']),
        new ORM\Index(name: 'idx_tournaments_game', columns: ['game_id']),
        new ORM\Index(name: 'idx_tournaments_status', columns: ['status', 'start_date']),
    ]
)]
class Tournament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'tournament_id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'organizer_user_id',
        referencedColumnName: 'user_id',
        nullable: false,
        onDelete: 'RESTRICT'
    )]
    private User $organizer;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'game_id', referencedColumnName: 'game_id', nullable: false, onDelete: 'RESTRICT')]
    private Game $game;

    #[ORM\Column(name: 'title', type: Types::STRING, length: 180)]
    private string $title;

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'rules', type: Types::TEXT, nullable: true)]
    private ?string $rules = null;

    #[ORM\Column(name: 'start_date', type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $startDate;

    #[ORM\Column(name: 'end_date', type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $endDate;

    #[ORM\Column(name: 'registration_deadline', type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $registrationDeadline = null;

    #[ORM\Column(name: 'max_teams', type: Types::INTEGER, options: ['unsigned' => true])]
    private int $maxTeams;

    #[ORM\Column(
        name: 'format',
        type: Types::STRING,
        length: 5,
        columnDefinition: "ENUM('BO1','BO3','BO5') NOT NULL DEFAULT 'BO1'"
    )]
    private string $format = 'BO1';

    #[ORM\Column(name: 'prize_pool', type: Types::DECIMAL, precision: 12, scale: 2, options: ['default' => 0])]
    private string $prizePool = '0.00';

    #[ORM\Column(name: 'prize_description', type: Types::STRING, length: 255, nullable: true)]
    private ?string $prizeDescription = null;

    #[ORM\Column(
        name: 'status',
        type: Types::STRING,
        length: 10,
        columnDefinition: "ENUM('DRAFT','OPEN','ONGOING','FINISHED','CANCELLED') NOT NULL DEFAULT 'DRAFT'"
    )]
    private string $status = 'DRAFT';

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
