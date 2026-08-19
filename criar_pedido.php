<?php
// criar_pedido.php — cria cliente/pedido/ficha e gera a cobrança Pix via Mercado Pago
// Recebe: $_SESSION['quiz'] (preenchido pelo quiz do produto) + ?produto= na URL
// Produz: $_SESSION['pix'] (lido pelo pagar.php) e redireciona pra lá
session_start();
require_once __DIR__ . '/config.php';

// Produtos habilitados neste momento — só musculação por enquanto
$produtos_disponiveis = [
    'musculacao' => [
        'label' => 'Acervo de Musculação — Sua Biblioteca',
        'valor' => MP_PRECO,
    ],
];

$produto = $_GET['produto'] ?? '';

if (empty($_SESSION['quiz']) || !isset($produtos_disponiveis[$produto])) {
    header('Location: index.php');
    exit;
}

$quiz      = $_SESSION['quiz'];
$valor     = $produtos_disponiveis[$produto]['valor'];
$descricao = $produtos_disponiveis[$produto]['label'];

$nome  = trim($quiz['nome'] ?? '');
$email = trim($quiz['email'] ?? '');

if ($nome === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: musculacao.php');
    exit;
}

try {
    // 1) Cliente — busca por e-mail, cria se não existir
    $stmt = $pdo->prepare('SELECT id FROM clientes WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $cliente = $stmt->fetch();

    if ($cliente) {
        $cliente_id = $cliente['id'];
    } else {
        $stmt = $pdo->prepare('INSERT INTO clientes (nome, email, telefone) VALUES (?, ?, ?)');
        $stmt->execute([$nome, $email, '']);
        $cliente_id = $pdo->lastInsertId();
    }

    // 2) Pedido
    $token_ficha = bin2hex(random_bytes(32));
    $stmt = $pdo->prepare('INSERT INTO pedidos (cliente_id, mp_status, valor, tipo_produto, token_ficha) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$cliente_id, 'pending', $valor, $produto, $token_ficha]);
    $pedido_id = $pdo->lastInsertId();

    // 3) Ficha — perfil do quiz em JSON; escolhas_aluno fica NULL até o pós-pagamento (selecao.php)
    $perfil_json = json_encode([
        'sexo'        => $quiz['sexo'] ?? 'M',
        'objetivo'    => $quiz['objetivo'] ?? '',
        'nivel'       => $quiz['nivel'] ?? 'Iniciante',
        'dias_semana' => $quiz['dias_semana'] ?? 3,
        'tempo'       => $quiz['tempo'] ?? '',
        'restricao'   => $quiz['restricao'] ?? 'nenhuma',
        'idade'       => $quiz['idade'] ?? '',
    ], JSON_UNESCAPED_UNICODE);

    $stmt = $pdo->prepare('INSERT INTO fichas (pedido_id, perfil, tipo_produto, dias_semana) VALUES (?, ?, ?, ?)');
    $stmt->execute([$pedido_id, $perfil_json, $produto, $quiz['dias_semana'] ?? 3]);

    // 4) Chama a API do Mercado Pago para gerar o Pix
    $body = [
        'transaction_amount' => (float)$valor,
        'description'        => $descricao,
        'payment_method_id'  => 'pix',
        'external_reference' => (string)$pedido_id,
        'notification_url'   => APP_URL . '/webhook.php',
        'payer' => [
            'email'      => $email,
            'first_name' => explode(' ', $nome)[0],
        ],
    ];

    $ch = curl_init('https://api.mercadopago.com/v1/payments');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . MP_ACCESS_TOKEN,
            'Content-Type: application/json',
            'X-Idempotency-Key: ' . $token_ficha,
        ],
        CURLOPT_POSTFIELDS => json_encode($body),
    ]);
    $resp      = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $mp = json_decode($resp, true);

    if ($http_code >= 300 || empty($mp['id']) || empty($mp['point_of_interaction']['transaction_data']['qr_code'])) {
        error_log('criar_pedido.php: falha ao criar Pix no Mercado Pago — pedido ' . $pedido_id . ' — resposta: ' . $resp);
        header('Location: index.php');
        exit;
    }

    // 5) Salva o payment_id já na criação (o webhook.php também atualiza depois, quando o status mudar)
    $stmt = $pdo->prepare('UPDATE pedidos SET mp_payment_id = ? WHERE id = ?');
    $stmt->execute([$mp['id'], $pedido_id]);

    // 6) Popula a sessão que o pagar.php espera encontrar
    $_SESSION['pix'] = [
        'qr_img'    => $mp['point_of_interaction']['transaction_data']['qr_code_base64'],
        'qr_code'   => $mp['point_of_interaction']['transaction_data']['qr_code'],
        'nome'      => $nome,
        'pedido_id' => $pedido_id,
        'token'     => $token_ficha,
    ];

    header('Location: pagar.php');
    exit;

} catch (Throwable $e) {
    error_log('criar_pedido.php erro: ' . $e->getMessage());
    header('Location: index.php');
    exit;
}