<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

const OLLAMA_BASE_URL = 'http://127.0.0.1:11434';
const OLLAMA_MODEL = 'llama3.1:latest';

function ollamaRespond(int $status, array $body): void
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function ollamaRequest(string $path, ?array $payload = null): array
{
    $headers = "Accept: application/json\r\n";
    $content = '';
    if ($payload !== null) {
        $headers .= "Content-Type: application/json\r\n";
        $content = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    $context = stream_context_create([
        'http' => [
            'method' => $payload === null ? 'GET' : 'POST',
            'header' => $headers,
            'content' => $content,
            'timeout' => $payload === null ? 4 : 180,
            'ignore_errors' => true
        ]
    ]);
    $response = @file_get_contents(OLLAMA_BASE_URL . $path, false, $context);
    if ($response === false) {
        throw new RuntimeException('Local Ollama service is not reachable.');
    }

    $status = 0;
    foreach ($http_response_header ?? [] as $header) {
        if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $header, $matches)) {
            $status = (int) $matches[1];
        }
    }
    $decoded = json_decode($response, true);
    if ($status < 200 || $status >= 300 || !is_array($decoded)) {
        throw new RuntimeException('Local Ollama returned an invalid response.');
    }
    return $decoded;
}

function ollamaText($value, int $maximumBytes): bool
{
    return is_string($value) && trim($value) !== '' && strlen($value) <= $maximumBytes;
}

function ollamaQuizQuestions($response, int $expectedCount): ?array
{
    if (!is_string($response)) return null;
    $quiz = json_decode(trim($response), true);
    if (!is_array($quiz) || !isset($quiz['questions']) || !is_array($quiz['questions'])
        || count($quiz['questions']) !== $expectedCount) {
        return null;
    }

    $questions = [];
    foreach ($quiz['questions'] as $question) {
        if (!is_array($question)
            || !ollamaText($question['question'] ?? null, 1200)
            || !isset($question['options'])
            || !is_array($question['options'])
            || count($question['options']) !== 4
            || !is_int($question['correctIndex'] ?? null)
            || $question['correctIndex'] < 0
            || $question['correctIndex'] > 3
            || !ollamaText($question['explanation'] ?? null, 2000)) {
            return null;
        }
        $options = array_values($question['options']);
        foreach ($options as $option) {
            if (!ollamaText($option, 600)) return null;
        }
        $normalizedOptions = array_map(
            static fn(string $option): string => function_exists('mb_strtolower')
                ? mb_strtolower($option)
                : strtolower($option),
            $options
        );
        if (count(array_unique($normalizedOptions)) !== 4) return null;
        $questions[] = [
            'question' => trim($question['question']),
            'options' => array_map('trim', $options),
            'correctIndex' => $question['correctIndex'],
            'explanation' => trim($question['explanation'])
        ];
    }
    return $questions;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'GET') {
    if (($_GET['action'] ?? '') !== 'status') {
        ollamaRespond(404, ['success' => false, 'error' => 'Ollama status resource was not found.']);
    }
    try {
        $tags = ollamaRequest('/api/tags');
        $models = array_values(array_filter(array_map(
            static fn($model) => is_array($model) && isset($model['name']) && is_string($model['name'])
                ? $model['name']
                : null,
            $tags['models'] ?? []
        )));
        ollamaRespond(200, [
            'success' => true,
            'available' => true,
            'model' => OLLAMA_MODEL,
            'modelAvailable' => in_array(OLLAMA_MODEL, $models, true),
            'models' => $models
        ]);
    } catch (Throwable $error) {
        error_log('Local Ollama status error: ' . $error->getMessage());
        ollamaRespond(200, [
            'success' => true,
            'available' => false,
            'model' => OLLAMA_MODEL,
            'modelAvailable' => false,
            'models' => []
        ]);
    }
}

if ($method !== 'POST') {
    header('Allow: GET, POST');
    ollamaRespond(405, ['success' => false, 'error' => 'Method not allowed.']);
}

try {
    $payload = json_decode(file_get_contents('php://input') ?: '', true, 32, JSON_THROW_ON_ERROR);
} catch (Throwable $error) {
    ollamaRespond(400, ['success' => false, 'error' => 'A valid JSON request body is required.']);
}
if (!is_array($payload) || !in_array($payload['task'] ?? null, ['chat', 'quiz'], true)) {
    ollamaRespond(400, ['success' => false, 'error' => 'A supported local AI task is required.']);
}

$task = $payload['task'];
try {
    if ($task === 'chat') {
        $prompt = $payload['prompt'] ?? null;
        $subject = $payload['subject'] ?? 'General studies';
        $socratic = ($payload['socraticMode'] ?? false) === true;
        $topicContext = $payload['topicContext'] ?? null;
        if (!ollamaText($prompt, 8000) || !ollamaText($subject, 300)) {
            ollamaRespond(400, ['success' => false, 'error' => 'The chat message or subject is invalid.']);
        }
        if ($topicContext !== null && (!is_array($topicContext)
            || !ollamaText($topicContext['title'] ?? null, 300)
            || !ollamaText($topicContext['description'] ?? '', 1500))) {
            ollamaRespond(400, ['success' => false, 'error' => 'The selected topic context is invalid.']);
        }

        $systemPrompt = $socratic
            ? "You are a Socratic tutor for {$subject}. Ask guiding questions and give hints without immediately revealing the final answer. Be accurate and age-appropriate."
            : "You are a helpful, accurate learning assistant for {$subject}. Explain clearly with concise steps, a relevant example when useful, and simple language.";
        if (is_array($topicContext)) {
            $systemPrompt .= "\nThe student's selected topic is: {$topicContext['title']}. Topic description: {$topicContext['description']}";
        }
        $generated = ollamaRequest('/api/generate', [
            'model' => OLLAMA_MODEL,
            'system' => $systemPrompt,
            'prompt' => trim($prompt),
            'stream' => false,
            'options' => ['temperature' => 0.4]
        ]);
        $answer = $generated['response'] ?? null;
        if (!ollamaText($answer, 30000)) {
            throw new RuntimeException('Local Ollama returned an empty chat response.');
        }
        ollamaRespond(200, [
            'success' => true,
            'data' => ['model' => OLLAMA_MODEL, 'response' => trim($answer)]
        ]);
    }

    $topic = $payload['topic'] ?? null;
    $subject = $payload['subject'] ?? 'General studies';
    $difficulty = $payload['difficulty'] ?? null;
    $count = filter_var($payload['count'] ?? null, FILTER_VALIDATE_INT);
    $studyText = $payload['studyText'] ?? '';
    if (!ollamaText($topic, 300) || !ollamaText($subject, 300)
        || !in_array($difficulty, ['Beginner', 'Intermediate', 'Advanced'], true)
        || !in_array($count, [3, 5, 10], true)
        || !is_string($studyText) || strlen($studyText) > 6000) {
        ollamaRespond(400, ['success' => false, 'error' => 'Quiz topic, subject, difficulty, count, or study text is invalid.']);
    }

    $quizSystemPrompt = 'You create accurate educational multiple-choice quizzes. Return only JSON matching this schema: {"questions":[{"question":"...","options":["...","...","...","..."],"correctIndex":0,"explanation":"..."}]}. Each question must have exactly four distinct options, and correctIndex must point to the truly correct option. Do not add markdown or text outside the JSON.';
    $quizPrompt = "Create exactly {$count} distinct {$difficulty}-level multiple-choice questions for the topic \"{$topic}\" in the subject \"{$subject}\". Provide a concise explanation for every answer. Do not duplicate questions.";
    if (trim($studyText) !== '') {
        $quizPrompt .= "\nUse only the following learner-provided study material as the source of facts:\n" . trim($studyText);
    }
    $generated = ollamaRequest('/api/generate', [
        'model' => OLLAMA_MODEL,
        'system' => $quizSystemPrompt,
        'prompt' => $quizPrompt,
        'format' => 'json',
        'stream' => false,
        'options' => ['temperature' => 0.2]
    ]);
    $questions = ollamaQuizQuestions($generated['response'] ?? null, $count);
    if ($questions === null) {
        error_log('Local Ollama returned malformed quiz data for topic: ' . $topic);
        ollamaRespond(502, ['success' => false, 'error' => 'The local model returned an invalid quiz. Please try generating it again.']);
    }
    ollamaRespond(200, [
        'success' => true,
        'data' => ['model' => OLLAMA_MODEL, 'questions' => $questions]
    ]);
} catch (Throwable $error) {
    error_log('Local Ollama generation error: ' . $error->getMessage());
    ollamaRespond(503, ['success' => false, 'error' => 'The local Ollama model could not generate a response. Check that llama3.1:latest is running and try again.']);
}
