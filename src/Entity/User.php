<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'users',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uq_users_username', columns: ['username']),
        new ORM\UniqueConstraint(name: 'uq_users_email', columns: ['email']),
    ],
    indexes: [
        new ORM\Index(name: 'idx_users_role', columns: ['role']),
        new ORM\Index(name: 'idx_users_profile_image_id', columns: ['profile_image_id']),
    ]
)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'user_id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'username', type: Types::STRING, length: 50)]
    private string $username;

    #[ORM\Column(name: 'email', type: Types::STRING, length: 190)]
    private string $email;

    #[ORM\Column(name: 'password_hash', type: Types::STRING, length: 255)]
    private string $passwordHash;

    #[ORM\Column(
        name: 'role',
        type: Types::STRING,
        length: 20,
        columnDefinition: "ENUM('PLAYER','ORGANIZER','ADMIN') NOT NULL DEFAULT 'PLAYER'"
    )]
    private string $role = 'PLAYER';

    #[ORM\Column(name: 'display_name', type: Types::STRING, length: 80)]
    private string $displayName;

    #[ORM\Column(name: 'bio', type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(name: 'phone', type: Types::STRING, length: 30, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(name: 'country', type: Types::STRING, length: 80, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(name: 'birth_date', type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $birthDate = null;

    #[ORM\Column(
        name: 'gender',
        type: Types::STRING,
        length: 20,
        nullable: true,
        columnDefinition: "ENUM('MALE','FEMALE','OTHER','UNKNOWN') NULL DEFAULT 'UNKNOWN'"
    )]
    private ?string $gender = 'UNKNOWN';

    #[ORM\Column(name: 'email_verified', type: Types::BOOLEAN, options: ['default' => 0])]
    private bool $emailVerified = false;

    #[ORM\Column(name: 'is_active', type: Types::BOOLEAN, options: ['default' => 1])]
    private bool $isActive = true;

    #[ORM\Column(name: 'last_login_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $lastLoginAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'profile_image_id',
        referencedColumnName: 'image_id',
        nullable: true,
        onDelete: 'SET NULL'
    )]
    private ?Image $profileImage = null;

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
