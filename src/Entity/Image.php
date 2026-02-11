<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'images',
    indexes: [
        new ORM\Index(name: 'idx_images_uploaded_by', columns: ['uploaded_by_user_id']),
    ]
)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'image_id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'file_url', type: Types::STRING, length: 500)]
    private string $fileUrl;

    #[ORM\Column(name: 'mime_type', type: Types::STRING, length: 60)]
    private string $mimeType;

    #[ORM\Column(name: 'size_bytes', type: Types::BIGINT, options: ['unsigned' => true])]
    private string $sizeBytes;

    #[ORM\Column(name: 'width', type: Types::INTEGER, nullable: true, options: ['unsigned' => true])]
    private ?int $width = null;

    #[ORM\Column(name: 'height', type: Types::INTEGER, nullable: true, options: ['unsigned' => true])]
    private ?int $height = null;

    #[ORM\Column(name: 'alt_text', type: Types::STRING, length: 255, nullable: true)]
    private ?string $altText = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'uploaded_by_user_id',
        referencedColumnName: 'user_id',
        nullable: true,
        onDelete: 'SET NULL'
    )]
    private ?User $uploadedByUser = null;

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
