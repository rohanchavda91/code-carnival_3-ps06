<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'explore-learning-db.php';

function exploreLearningRespond(int $status, array $body): void
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function exploreLearningPositiveId($value): ?int
{
    if (!is_string($value) || !ctype_digit($value)) {
        return null;
    }

    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id === false ? null : $id;
}

try {
    $learnerKey = exploreLearningLearnerKey();
    $database = exploreLearningDatabase();
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $resource = isset($_GET['resource']) && is_string($_GET['resource']) ? $_GET['resource'] : '';

    if ($method === 'GET' && $resource === 'departments') {
        $rows = $database->query(
            'SELECT id, code, name, description FROM departments ORDER BY name COLLATE NOCASE'
        )->fetchAll();
        exploreLearningRespond(200, ['success' => true, 'data' => $rows]);
    }

    if ($method === 'GET' && $resource === 'subjects') {
        $departmentId = exploreLearningPositiveId($_GET['departmentId'] ?? null);
        if ($departmentId === null) {
            exploreLearningRespond(400, ['success' => false, 'error' => 'A valid departmentId is required.']);
        }
        $query = $database->prepare(
            'SELECT id, department_id, code, name, description
             FROM subjects WHERE department_id = :department_id ORDER BY name COLLATE NOCASE'
        );
        $query->execute(['department_id' => $departmentId]);
        exploreLearningRespond(200, ['success' => true, 'data' => $query->fetchAll()]);
    }

    if ($method === 'GET' && $resource === 'topics') {
        $subjectId = exploreLearningPositiveId($_GET['subjectId'] ?? null);
        if ($subjectId === null) {
            exploreLearningRespond(400, ['success' => false, 'error' => 'A valid subjectId is required.']);
        }
        $query = $database->prepare(
            'SELECT id, subject_id, title, description, chapter_number, difficulty
             FROM topics WHERE subject_id = :subject_id ORDER BY sort_order, id'
        );
        $query->execute(['subject_id' => $subjectId]);
        exploreLearningRespond(200, ['success' => true, 'data' => $query->fetchAll()]);
    }

    if ($method === 'GET' && $resource === 'progress') {
        $query = $database->prepare(
            'SELECT topic_id, status FROM learning_progress WHERE learner_key = :learner_key'
        );
        $query->execute(['learner_key' => $learnerKey]);
        exploreLearningRespond(200, ['success' => true, 'data' => $query->fetchAll()]);
    }

    if ($method === 'POST' && $resource === 'progress') {
        $payload = json_decode(file_get_contents('php://input') ?: '', true);
        if (!is_array($payload)) {
            exploreLearningRespond(400, ['success' => false, 'error' => 'A JSON request body is required.']);
        }
        $topicIdValue = $payload['topicId'] ?? null;
        $topicId = is_int($topicIdValue) || is_string($topicIdValue)
            ? exploreLearningPositiveId((string) $topicIdValue)
            : null;
        $status = $payload['status'] ?? null;
        if ($topicId === null || !in_array($status, ['not_started', 'in_progress', 'completed'], true)) {
            exploreLearningRespond(400, ['success' => false, 'error' => 'A valid topicId and progress status are required.']);
        }
        $topicExists = $database->prepare('SELECT 1 FROM topics WHERE id = :topic_id');
        $topicExists->execute(['topic_id' => $topicId]);
        if (!$topicExists->fetchColumn()) {
            exploreLearningRespond(404, ['success' => false, 'error' => 'Topic was not found.']);
        }
        if ($status === 'not_started') {
            $query = $database->prepare(
                'DELETE FROM learning_progress WHERE learner_key = :learner_key AND topic_id = :topic_id'
            );
            $query->execute(['learner_key' => $learnerKey, 'topic_id' => $topicId]);
            exploreLearningRespond(200, ['success' => true, 'data' => ['topicId' => $topicId, 'status' => $status]]);
        }
        $query = $database->prepare(
            'INSERT INTO learning_progress (learner_key, topic_id, status, updated_at)
             VALUES (:learner_key, :topic_id, :status, CURRENT_TIMESTAMP)
             ON CONFLICT (learner_key, topic_id) DO UPDATE SET
               status = excluded.status,
               updated_at = CURRENT_TIMESTAMP'
        );
        $query->execute([
            'learner_key' => $learnerKey,
            'topic_id' => $topicId,
            'status' => $status
        ]);
        exploreLearningRespond(200, ['success' => true, 'data' => ['topicId' => $topicId, 'status' => $status]]);
    }

    if (!in_array($method, ['GET', 'POST'], true)) {
        header('Allow: GET, POST');
        exploreLearningRespond(405, ['success' => false, 'error' => 'Method not allowed.']);
    }

    exploreLearningRespond(404, ['success' => false, 'error' => 'Explore Learning resource was not found.']);
} catch (Throwable $error) {
    error_log('Explore Learning API error: ' . $error->getMessage());
    exploreLearningRespond(500, ['success' => false, 'error' => 'The learning catalog is temporarily unavailable.']);
}
