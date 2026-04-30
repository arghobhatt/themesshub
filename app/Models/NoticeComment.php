<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

class NoticeComment
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    
    public function create(int $noticeId, int $userId, string $content): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO notice_comments (notice_id, user_id, content, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ");
        return $stmt->execute([$noticeId, $userId, $content]);
    }

   
    public function getByNotice(int $noticeId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT nc.*, u.full_name as user_name
            FROM notice_comments nc
            INNER JOIN users u ON nc.user_id = u.id
            WHERE nc.notice_id = ?
            ORDER BY nc.created_at ASC
        ");
        $stmt->execute([$noticeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function delete(int $commentId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM notice_comments
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$commentId, $userId]);
    }
}