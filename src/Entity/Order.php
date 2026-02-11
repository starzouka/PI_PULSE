<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'orders',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uq_orders_order_number', columns: ['order_number']),
        new ORM\UniqueConstraint(name: 'uq_orders_cart', columns: ['cart_id']),
    ],
    indexes: [
        new ORM\Index(name: 'idx_orders_user', columns: ['user_id']),
        new ORM\Index(name: 'idx_orders_status', columns: ['status', 'created_at']),
    ]
)]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'order_id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'order_number', type: Types::STRING, length: 30)]
    private string $orderNumber;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(
        name: 'cart_id',
        referencedColumnName: 'cart_id',
        nullable: false,
        unique: true,
        onDelete: 'RESTRICT'
    )]
    private Cart $cart;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'user_id',
        referencedColumnName: 'user_id',
        nullable: false,
        onDelete: 'RESTRICT'
    )]
    private User $user;

    #[ORM\Column(
        name: 'status',
        type: Types::STRING,
        length: 20,
        columnDefinition: "ENUM('PENDING','PAID','CANCELLED','SHIPPED','DELIVERED') NOT NULL DEFAULT 'PENDING'"
    )]
    private string $status = 'PENDING';

    #[ORM\Column(
        name: 'payment_method',
        type: Types::STRING,
        length: 10,
        nullable: true,
        columnDefinition: "ENUM('CARD','CASH','OTHER') NULL"
    )]
    private ?string $paymentMethod = null;

    #[ORM\Column(
        name: 'payment_status',
        type: Types::STRING,
        length: 10,
        columnDefinition: "ENUM('UNPAID','PAID','REFUNDED') NOT NULL DEFAULT 'UNPAID'"
    )]
    private string $paymentStatus = 'UNPAID';

    #[ORM\Column(name: 'total_amount', type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $totalAmount;

    #[ORM\Column(name: 'shipping_address', type: Types::STRING, length: 255, nullable: true)]
    private ?string $shippingAddress = null;

    #[ORM\Column(name: 'phone_for_delivery', type: Types::STRING, length: 30, nullable: true)]
    private ?string $phoneForDelivery = null;

    #[ORM\Column(
        name: 'created_at',
        type: Types::DATETIME_IMMUTABLE,
        columnDefinition: 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP'
    )]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'paid_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $paidAt = null;

    #[ORM\Column(name: 'shipped_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $shippedAt = null;

    #[ORM\Column(name: 'delivered_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $deliveredAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
