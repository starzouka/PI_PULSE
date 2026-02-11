<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'post_images',
    indexes: [
        new ORM\Index(name: 'idx_post_images_image', columns: ['image_id']),
    ]
)]
class PostImage
{
    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'post_id', referencedColumnName: 'post_id', nullable: false, onDelete: 'CASCADE')]
    private Post $post;

    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'image_id', referencedColumnName: 'image_id', nullable: false, onDelete: 'RESTRICT')]
    private Image $image;

    #[ORM\Column(name: 'position', type: Types::INTEGER, options: ['unsigned' => true, 'default' => 1])]
    private int $position = 1;
}
