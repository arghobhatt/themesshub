<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class Mess
{
    private PDO $db;

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../config/database.php';
    }

    public function create(string $name, string $location, ?string $rent, ?string $description, int $createdBy, ?string $image = null): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO messes (name, location, rent, description, created_by, image)
             VALUES (:name, :location, :rent, :description, :created_by, :image)'
        );
        $stmt->execute([
            'name' => $name,
            'location' => $location,
            'rent' => $rent,
            'description' => $description,
            'created_by' => $createdBy,
            'image' => $image,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, location, rent, description, created_by, started_on, is_active, image
             FROM messes
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $mess = $stmt->fetch();

        return $mess !== false ? $mess : null;
    }

    public function update(int $id, string $name, string $location, ?string $rent, ?string $description, ?string $image = null): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE messes
             SET name = :name,
                 location = :location,
                 rent = :rent,
                 description = :description,
                 image = COALESCE(:image, image)
             WHERE id = :id'
        );

        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'location' => $location,
            'rent' => $rent,
            'description' => $description,
            'image' => $image,
        ]);
    }

    public function listActive(int $limit = 12): array
    {
        $limit = max(1, min($limit, 50));

        $stmt = $this->db->prepare(
            'SELECT id, name, location, rent, description, image
             FROM messes
             WHERE is_active = 1
             ORDER BY id DESC
             LIMIT :limit'
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function canManage(int $messId, int $userId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1
            FROM messes m
            LEFT JOIN mess_memberships mm
            ON mm.mess_id = m.id
            AND mm.user_id = :user_id
            AND mm.role_in_mess = "manager"
            AND mm.status = "active"
            WHERE m.id = :mess_id
            AND (m.created_by = :user_id_alt OR mm.id IS NOT NULL) 
            LIMIT 1'
        );
        $stmt->execute([
            'mess_id' => $messId,
            'user_id' => $userId,
            'user_id_alt' => $userId, 
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function listManagedBy(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT DISTINCT m.id, m.name, m.location
             FROM messes m
             LEFT JOIN mess_memberships mm
               ON mm.mess_id = m.id
              AND mm.user_id = :user_id
              AND mm.role_in_mess = "manager"
              AND mm.status = "active"
             WHERE m.created_by = :user_id OR mm.id IS NOT NULL
             ORDER BY m.name'
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }
}
