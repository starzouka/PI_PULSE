<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'products',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uq_products_sku', columns: ['sku']),
    ],
    indexes: [
        new ORM\Index(name: 'idx_products_team', columns: ['team_id']),
        new ORM\Index(name: 'idx_products_active', columns: ['is_active']),
    ]
)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'product_id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'team_id',
        referencedColumnName: 'team_id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    private Team $team;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 150)]
    private string $name;

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'price', type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $price;

    #[ORM\Column(name: 'stock_qty', type: Types::INTEGER, options: ['unsigned' => true, 'default' => 0])]
    private int $stockQty = 0;

    #[ORM\Column(name: 'sku', type: Types::STRING, length: 64, nullable: true)]
    private ?string $sku = null;

    #[ORM\Column(name: 'is_active', type: Types::BOOLEAN, options: ['default' => 1])]
    private bool $isActive = true;

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
