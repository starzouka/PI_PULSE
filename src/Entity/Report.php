<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'reports',
    indexes: [
        new ORM\Index(name: 'idx_reports_reporter', columns: ['reporter_user_id']),
        new ORM\Index(name: 'idx_reports_status', columns: ['status', 'created_at']),
        new ORM\Index(name: 'idx_reports_handler', columns: ['handled_by_admin_id']),
    ]
)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'report_id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'reporter_user_id',
        referencedColumnName: 'user_id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    private User $reporter;

    #[ORM\Column(
        name: 'target_type',
        type: Types::STRING,
        length: 10,
        columnDefinition: "ENUM('POST','COMMENT','USER','TEAM') NOT NULL"
    )]
    private string $targetType;

    #[ORM\Column(name: 'target_id', type: Types::BIGINT, options: ['unsigned' => true])]
    private string $targetId;

    #[ORM\Column(name: 'reason', type: Types::TEXT)]
    private string $reason;

    #[ORM\Column(
        name: 'status',
        type: Types::STRING,
        length: 20,
        columnDefinition: "ENUM('OPEN','IN_REVIEW','CLOSED') NOT NULL DEFAULT 'OPEN'"
    )]
    private string $status = 'OPEN';

    #[ORM\Column(
        name: 'created_at',
        type: Types::DATETIME_IMMUTABLE,
        columnDefinition: 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP'
    )]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'handled_by_admin_id',
        referencedColumnName: 'user_id',
        nullable: true,
        onDelete: 'SET NULL'
    )]
    private ?User $handledByAdmin = null;

    #[ORM\Column(name: 'handled_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $handledAt = null;

    #[ORM\Column(name: 'admin_note', type: Types::TEXT, nullable: true)]
    private ?string $adminNote = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
