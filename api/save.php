<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// リクエストボディ取得
$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!$data || !isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request: id is required']);
    exit;
}

// IDのバリデーション（UUID形式のみ許可）
$id = $data['id'];
if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid id format']);
    exit;
}

// dataディレクトリの準備
$dataDir = __DIR__ . '/../data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

// ファイルに保存
$filePath = $dataDir . '/' . $id . '.json';
$saveData = $data['payload'] ?? $data;
$result = file_put_contents($filePath, json_encode($saveData, JSON_UNESCAPED_UNICODE));

if ($result === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save data']);
    exit;
}

echo json_encode(['success' => true, 'id' => $id]);
