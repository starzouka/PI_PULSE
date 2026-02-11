<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'games',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uq_games_name', columns: ['name']),
    ],
    indexes: [
        new ORM\Index(name: 'idx_games_category_id', columns: ['category_id']),
        new ORM\Index(name: 'idx_games_cover_image_id', columns: ['cover_image_id']),
    ]
)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'game_id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'category_id',
        referencedColumnName: 'category_id',
        nullable: false,
        onDelete: 'RESTRICT'
    )]
    private Category $category;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 120)]
    private string $name;

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'publisher', type: Types::STRING, length: 120, nullable: true)]
    private ?string $publisher = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'cover_image_id',
        referencedColumnName: 'image_id',
        nullable: true,
        onDelete: 'SET NULL'
    )]
    private ?Image $coverImage = null;

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
