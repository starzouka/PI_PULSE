<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'product_images',
    indexes: [
        new ORM\Index(name: 'idx_product_images_image', columns: ['image_id']),
    ]
)]
class ProductImage
{
    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'product_id',
        referencedColumnName: 'product_id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    private Product $product;

    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'image_id', referencedColumnName: 'image_id', nullable: false, onDelete: 'RESTRICT')]
    private Image $image;

    #[ORM\Column(name: 'position', type: Types::INTEGER, options: ['unsigned' => true, 'default' => 1])]
    private int $position = 1;
}
