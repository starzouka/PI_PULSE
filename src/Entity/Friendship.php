<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'friendships',
    indexes: [
        new ORM\Index(name: 'idx_friendships_user2', columns: ['user_id2']),
    ]
)]
class Friendship
{
    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'user_id1', referencedColumnName: 'user_id', nullable: false, onDelete: 'CASCADE')]
    private User $user1;

    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'user_id2', referencedColumnName: 'user_id', nullable: false, onDelete: 'CASCADE')]
    private User $user2;

    #[ORM\Column(
        name: 'created_at',
        type: Types::DATETIME_IMMUTABLE,
        columnDefinition: 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP'
    )]
    private \DateTimeImmutable $createdAt;
}
