<?php
// status.php — Retorna status do pedido (chamado pelo JS polling)
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

$pedido_id = filter_input(INPUT_GET, 'pedido_id', FILTER_VALIDATE_INT);
if (!$pedido_id) {
    echo json_encode(['status' => 'invalid']);
    exit;
}

$stmt = $pdo->prepare('SELECT mp_status FROM pedidos WHERE id = ? LIMIT 1');
$stmt->execute([$pedido_id]);
$pedido = $stmt->fetch();

if (!$pedido) {
    echo json_encode(['status' => 'not_found']);
    exit;
}

echo json_encode(['status' => $pedido['mp_status']]);
