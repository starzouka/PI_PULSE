<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'teams',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uq_teams_name', columns: ['name']),
    ],
    indexes: [
        new ORM\Index(name: 'idx_teams_captain', columns: ['captain_user_id']),
        new ORM\Index(name: 'idx_teams_logo', columns: ['logo_image_id']),
    ]
)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'team_id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 100)]
    private string $name;

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'region', type: Types::STRING, length: 80, nullable: true)]
    private ?string $region = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'logo_image_id',
        referencedColumnName: 'image_id',
        nullable: true,
        onDelete: 'SET NULL'
    )]
    private ?Image $logoImage = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'captain_user_id',
        referencedColumnName: 'user_id',
        nullable: false,
        onDelete: 'RESTRICT'
    )]
    private User $captain;

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
