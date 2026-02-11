<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'match_teams',
    indexes: [
        new ORM\Index(name: 'idx_match_teams_team', columns: ['team_id']),
    ]
)]
class MatchTeam
{
    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'match_id', referencedColumnName: 'match_id', nullable: false, onDelete: 'CASCADE')]
    private GameMatch $match;

    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'team_id', referencedColumnName: 'team_id', nullable: false, onDelete: 'CASCADE')]
    private Team $team;

    #[ORM\Column(name: 'score', type: Types::INTEGER, nullable: true, options: ['unsigned' => true])]
    private ?int $score = null;

    #[ORM\Column(name: 'is_winner', type: Types::BOOLEAN, nullable: true)]
    private ?bool $isWinner = null;
}
