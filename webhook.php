<?php
// webhook.php — Recebe notificações do Mercado Pago
require_once __DIR__ . '/config.php';

$autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}
use PHPMailer\PHPMailer\PHPMailer;

// Loga o payload bruto para debug
$payload = file_get_contents('php://input');
$pdo->prepare('INSERT INTO webhook_logs (payload) VALUES (?)')->execute([$payload]);

$data = json_decode($payload, true);

// MP envia type=payment quando um pagamento muda de status
if (($data['type'] ?? '') !== 'payment') {
    http_response_code(200);
    exit;
}

$mp_payment_id = $data['data']['id'] ?? null;
if (!$mp_payment_id) {
    http_response_code(200);
    exit;
}

// Consulta o status real na API do MP
$ch = curl_init("https://api.mercadopago.com/v1/payments/{$mp_payment_id}");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . MP_ACCESS_TOKEN],
]);
$resp = curl_exec($ch);
curl_close($ch);
$mp = json_decode($resp, true);

$status              = $mp['status'] ?? 'pending';
$external_reference  = $mp['external_reference'] ?? null; // pedido_id

if (!$external_reference) {
    http_response_code(200);
    exit;
}

// Atualiza status do pedido
$pdo->prepare('UPDATE pedidos SET mp_status = ?, mp_payment_id = ? WHERE id = ?')
    ->execute([$status, $mp_payment_id, $external_reference]);

// Se aprovado, envia e-mail (a ficha já foi gerada e salva no index.php)
if ($status === 'approved') {
    $stmt = $pdo->prepare('
        SELECT p.id, p.token_ficha, p.ficha_enviada, c.nome, c.email
        FROM pedidos p
        JOIN clientes c ON c.id = p.cliente_id
        WHERE p.id = ?
        LIMIT 1
    ');
    $stmt->execute([$external_reference]);
    $pedido = $stmt->fetch();

    if ($pedido && !$pedido['ficha_enviada']) {
        // Confirma que a ficha existe (deveria ter sido criada no index.php)
        $stmtFicha = $pdo->prepare('SELECT id FROM fichas WHERE pedido_id = ? LIMIT 1');
        $stmtFicha->execute([$pedido['id']]);
        $fichaExiste = $stmtFicha->fetch();

        if ($fichaExiste) {
            enviarEmailFicha($pedido['nome'], $pedido['email'], $pedido['token_ficha']);

            $pdo->prepare('UPDATE pedidos SET ficha_enviada = 1 WHERE id = ?')
                ->execute([$pedido['id']]);
        } else {
            error_log('Webhook: pedido ' . $pedido['id'] . ' aprovado mas sem ficha gerada.');
        }
    }
}

http_response_code(200);
echo 'OK';

// ─────────────────────────────────────────────
function enviarEmailFicha(string $nome, string $email, string $token): void
{
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        error_log('PHPMailer não disponível — e-mail não enviado para ' . $email);
        return;
    }

    $link = APP_URL . '/ficha.php?t=' . $token;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USER;
        $mail->Password   = MAIL_PASS;
        $mail->SMTPSecure = MAIL_SECURE;
        $mail->Port       = MAIL_PORT;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($email, $nome);
        $mail->Subject = '💪 Sua ficha de treino ClicTreino está pronta!';
        $mail->isHTML(true);
        $mail->Body = "
        <div style='font-family:Inter,sans-serif;background:#0A0A0A;color:#F0F0F0;padding:32px;border-radius:12px;max-width:480px;margin:auto'>
            <h1 style='color:#00C853;font-size:1.8rem;margin-bottom:8px'>ClicTreino 💪</h1>
            <p>Olá, <strong>{$nome}</strong>!</p>
            <p style='margin-top:12px;color:#aaa'>Seu pagamento foi confirmado. Acesse sua ficha de treino pelo link abaixo:</p>
            <a href='{$link}' style='display:inline-block;margin-top:20px;background:#00C853;color:#000;font-weight:700;padding:14px 28px;border-radius:8px;text-decoration:none'>
                ACESSAR MINHA FICHA
            </a>
            <p style='margin-top:20px;font-size:.8rem;color:#555'>Salve este e-mail ou o link para acessar sua ficha quando quiser.</p>
        </div>";
        $mail->AltBody = "Sua ficha ClicTreino está pronta! Acesse: {$link}";
        $mail->send();
    } catch (\Exception $e) {
        error_log('PHPMailer erro: ' . $e->getMessage());
    }
}