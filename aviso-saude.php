<?php
// aviso-saude.php — ClicTreino
// Aviso de Saúde — referenciado nas cláusulas 4.1-4.3 dos Termos de Uso
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Aviso de Saúde — ClicTreino</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
    background: #0f1115;
    color: #e8e8e8;
    line-height: 1.6;
    font-size: 16px;
    -webkit-text-size-adjust: 100%;
  }
  .container { max-width: 100%; padding: 20px 18px 60px; }
  header.page-header {
    position: sticky; top: 0; background: #0f1115;
    padding: 14px 18px; border-bottom: 1px solid #23262e; z-index: 10;
  }
  header.page-header h1 { font-size: 18px; font-weight: 700; color: #ffffff; }
  header.page-header .updated { font-size: 12px; color: #9a9a9a; margin-top: 2px; }
  h2 { font-size: 16px; font-weight: 700; color: #4fb6ff; margin: 28px 0 10px; }
  h2 .num { color: #6c7280; margin-right: 6px; }
  p { font-size: 14.5px; color: #d2d2d6; margin-bottom: 10px; }
  strong { color: #fff; }
  ul { margin: 8px 0 14px 18px; }
  li { font-size: 14.5px; color: #d2d2d6; margin-bottom: 6px; }
  .alert-box {
    background: #2a1414; border: 1px solid #4a2222; border-left: 4px solid #ff5c5c;
    border-radius: 8px; padding: 14px 16px; margin: 18px 0; font-size: 14px; color: #ffd6d6;
  }
  .alert-box strong { color: #ff8c8c; }
  .placeholder-box {
    background: #1a1d24; border: 1px solid #2a2e38; border-left: 3px solid #4fb6ff;
    border-radius: 8px; padding: 12px 14px; margin: 18px 0; font-size: 13px; color: #b6c2d1;
  }
  .legal-item { margin-bottom: 10px; padding-left: 14px; border-left: 2px solid #23262e; }
  .legal-item b { color: #fff; }
  .back-link { display: inline-block; margin-top: 30px; font-size: 14px; color: #4fb6ff; text-decoration: none; }
  .toc { background: #1a1d24; border: 1px solid #2a2e38; border-radius: 10px; padding: 14px 16px; margin-bottom: 24px; }
  .toc a { display: block; font-size: 13.5px; color: #c9d3e0; text-decoration: none; padding: 5px 0; border-bottom: 1px solid #23262e; }
  .toc a:last-child { border-bottom: none; }
</style>
</head>
<body>

<header class="page-header">
  <h1>Aviso de Saúde — ClicTreino</h1>
  <div class="updated">Última atualização: <?php echo "01/07/2026"; // TODO: ajustar data real de publicação ?></div>
</header>

<div class="container">

  <div class="alert-box">
    <strong>Leia com atenção antes de iniciar qualquer treino do Acervo ClicTreino.</strong> Este aviso é parte integrante dos nossos <a href="termos.php" style="color:#ffb3b3;">Termos de Uso</a>.
  </div>

  <div class="toc">
    <a href="#sec1">1. Natureza do conteúdo</a>
    <a href="#sec2">2. Recomendação de avaliação prévia</a>
    <a href="#sec3">3. Situações que exigem atenção redobrada</a>
    <a href="#sec4">4. Sinais de alerta durante o treino</a>
    <a href="#sec5">5. Responsabilidade do usuário</a>
    <a href="#sec6">6. Limitação de responsabilidade da ClicTreino</a>
    <a href="#sec7">7. Base legal deste aviso</a>
  </div>

  <h2 id="sec1"><span class="num">1.</span>Natureza do conteúdo</h2>
  <p>O Acervo de Treinos ClicTreino oferece conteúdo de caráter geral e educativo, organizado em formato de catálogo, revisado periodicamente por profissional de Educação Física habilitado. <strong>Não se trata de prescrição médica, avaliação física individualizada ou acompanhamento personalizado.</strong></p>

  <h2 id="sec2"><span class="num">2.</span>Recomendação de avaliação prévia</h2>
  <p>Antes de iniciar qualquer treino do Acervo, recomendamos fortemente que você consulte um médico e, se possível, um profissional de Educação Física para avaliação individual da sua condição física, especialmente se você:</p>
  <ul>
    <li>Não pratica atividade física regularmente há mais de 6 meses;</li>
    <li>Tem mais de 45 anos;</li>
    <li>Possui histórico familiar de doenças cardíacas;</li>
    <li>Tem qualquer condição de saúde preexistente (cardíaca, respiratória, articular, metabólica, etc.).</li>
  </ul>

  <h2 id="sec3"><span class="num">3.</span>Situações que exigem atenção redobrada</h2>
  <p>Procure orientação médica específica e <strong>não inicie o treino sem liberação profissional</strong> caso você esteja em alguma das situações abaixo:</p>
  <ul>
    <li>Gestação ou puerpério recente;</li>
    <li>Lesão muscular, articular ou óssea recente ou em recuperação;</li>
    <li>Cirurgia recente (especialmente ortopédica, cardíaca ou abdominal);</li>
    <li>Uso de medicamentos que afetam pressão arterial, frequência cardíaca ou equilíbrio;</li>
    <li>Diagnóstico de doença cardiovascular, respiratória crônica, diabetes não controlada ou hipertensão não controlada.</li>
  </ul>

  <h2 id="sec4"><span class="num">4.</span>Sinais de alerta durante o treino</h2>
  <p><strong>Interrompa o exercício imediatamente</strong> e procure atendimento médico se sentir, durante ou após o treino:</p>
  <ul>
    <li>Dor no peito, falta de ar incomum ou palpitações;</li>
    <li>Tontura, desmaio ou visão turva;</li>
    <li>Dor articular ou muscular aguda e intensa;</li>
    <li>Náusea severa ou mal-estar geral fora do esperado para o esforço.</li>
  </ul>

  <h2 id="sec5"><span class="num">5.</span>Responsabilidade do usuário</h2>
  <p><strong>5.1.</strong> Ao escolher e executar qualquer treino do Acervo, você reconhece que está fazendo isso por sua própria conta e risco, com base na sua autoavaliação de capacidade física e, idealmente, após orientação médica/profissional prévia.</p>
  <p><strong>5.2.</strong> Você é responsável por ajustar a intensidade, carga e execução dos exercícios à sua própria condição, e por interromper a atividade diante de qualquer sinal de alerta descrito na Seção 4.</p>

  <h2 id="sec6"><span class="num">6.</span>Limitação de responsabilidade da ClicTreino</h2>
  <p>A ClicTreino não se responsabiliza por lesões, agravamento de condições preexistentes ou outros danos decorrentes da execução dos treinos do Acervo sem observância das recomendações deste Aviso de Saúde, da avaliação médica prévia recomendada, ou da interrupção da atividade diante de sinais de alerta.</p>

  <h2 id="sec7"><span class="num">7.</span>Base legal deste aviso</h2>
  <div class="legal-item"><b>a) Dever de informação</b> — art. 6º, III, do Código de Defesa do Consumidor (Lei nº 8.078/1990), que assegura ao consumidor informação clara e adequada sobre riscos do produto/serviço.</div>
  <div class="legal-item"><b>b) Natureza não-prescritiva do conteúdo</b> — reforça o disposto na cláusula 4 dos <a href="termos.php" style="color:#4fb6ff;">Termos de Uso</a>, afastando a caracterização de prescrição individualizada de exercício físico.</div>
  <div class="legal-item"><b>c) Responsabilidade civil</b> — arts. 186 e 927 do Código Civil (Lei nº 10.406/2002), que condicionam a responsabilização à comprovação de nexo causal e culpa, reforçando que o uso adequado das recomendações deste aviso é condição para a segurança do usuário.</div>

  <div class="placeholder-box">
    <strong>⚠️ Item pendente — implementação no fluxo de pagamento:</strong><br>
    Este aviso deve ser apresentado com <strong>checkbox obrigatório</strong> ("Li e estou de acordo com o Aviso de Saúde") antes da confirmação do pagamento em <code>pagar.php</code>. Implementação de código separada — ver checklist Fase 0.
  </div>

  <a href="index.php" class="back-link">&larr; Voltar</a>

</div>
</body>
</html>