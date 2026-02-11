<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'cart_items',
    indexes: [
        new ORM\Index(name: 'idx_cart_items_product', columns: ['product_id']),
    ]
)]
class CartItem
{
    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'cart_id', referencedColumnName: 'cart_id', nullable: false, onDelete: 'CASCADE')]
    private Cart $cart;

    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'product_id',
        referencedColumnName: 'product_id',
        nullable: false,
        onDelete: 'RESTRICT'
    )]
    private Product $product;

    #[ORM\Column(name: 'quantity', type: Types::INTEGER, options: ['unsigned' => true, 'default' => 1])]
    private int $quantity = 1;

    #[ORM\Column(name: 'unit_price_at_add', type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $unitPriceAtAdd;

    #[ORM\Column(
        name: 'added_at',
        type: Types::DATETIME_IMMUTABLE,
        columnDefinition: 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP'
    )]
    private \DateTimeImmutable $addedAt;

    #[ORM\Column(
        name: 'updated_at',
        type: Types::DATETIME_IMMUTABLE,
        columnDefinition: 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
    )]
    private \DateTimeImmutable $updatedAt;
}
