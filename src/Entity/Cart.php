<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'carts',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uq_carts_user', columns: ['user_id']),
    ]
)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'cart_id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(
        name: 'user_id',
        referencedColumnName: 'user_id',
        nullable: false,
        unique: true,
        onDelete: 'CASCADE'
    )]
    private User $user;

    #[ORM\Column(
        name: 'status',
        type: Types::STRING,
        length: 10,
        columnDefinition: "ENUM('OPEN','LOCKED','ORDERED') NOT NULL DEFAULT 'OPEN'"
    )]
    private string $status = 'OPEN';

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

    #[ORM\Column(name: 'locked_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $lockedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
