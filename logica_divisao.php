<?php
// ============================================================
// logica_divisao.php — ClicTreino
// Divisão equilibrada de grupos musculares por dias da semana
// ============================================================

/**
 * Divisão equilibrada e fixa por número de dias.
 * Cada treino tem grupos musculares sinérgicos e equilibrados.
 * A partir de 3 dias: um grupo por treino, bem dividido.
 */
function getDivisaoSugerida(int $dias, string $sexo = 'M'): array
{
    // Base: 5 treinos equilibrados (usados por todos os planos)
    $base = [
        'A' => ['nome' => 'Treino A — Peito + Tríceps',    'grupos' => ['Peitoral', 'Tríceps']],
        'B' => ['nome' => 'Treino B — Costas + Bíceps',    'grupos' => ['Costas', 'Bíceps']],
        'C' => ['nome' => 'Treino C — Pernas completo',    'grupos' => ['Pernas', 'Panturrilhas']],
        'D' => ['nome' => 'Treino D — Ombros + Trapézio',  'grupos' => ['Ombros', 'Trapézio']],
        'E' => ['nome' => 'Treino E — Glúteos + Core',     'grupos' => ['Glúteos', 'Eretores da espinha', 'Antebraços']],
    ];

    // Ajuste feminino: prioriza glúteos e pernas
    if ($sexo === 'F') {
        $base['A'] = ['nome' => 'Treino A — Glúteos + Pernas',   'grupos' => ['Glúteos', 'Pernas']];
        $base['B'] = ['nome' => 'Treino B — Costas + Bíceps',    'grupos' => ['Costas', 'Bíceps']];
        $base['C'] = ['nome' => 'Treino C — Peitoral + Tríceps', 'grupos' => ['Peitoral', 'Tríceps']];
        $base['D'] = ['nome' => 'Treino D — Pernas + Panturrilhas', 'grupos' => ['Pernas', 'Panturrilhas']];
        $base['E'] = ['nome' => 'Treino E — Ombros + Core',      'grupos' => ['Ombros', 'Trapézio', 'Antebraços']];
    }

    // 2 dias: Full Body dividido em superior e inferior
    if ($dias <= 2) {
        return [
            ['letra' => 'A', 'nome' => 'Treino A — Superior',
             'grupos' => $sexo === 'F'
                ? ['Peitoral', 'Costas', 'Ombros', 'Bíceps', 'Tríceps']
                : ['Peitoral', 'Costas', 'Ombros', 'Bíceps', 'Tríceps']],
            ['letra' => 'B', 'nome' => 'Treino B — Inferior',
             'grupos' => $sexo === 'F'
                ? ['Glúteos', 'Pernas', 'Panturrilhas', 'Eretores da espinha']
                : ['Pernas', 'Glúteos', 'Panturrilhas', 'Eretores da espinha']],
        ];
    }

    // 3 a 6 dias: usa os blocos base na quantidade certa
    $letras = array_slice(['A','B','C','D','E'], 0, min($dias, 5));
    $resultado = [];
    foreach ($letras as $letra) {
        $resultado[] = array_merge(['letra' => $letra], $base[$letra]);
    }

    // 6 dias: adiciona treino F livre
    if ($dias >= 6) {
        $resultado[] = [
            'letra'  => 'F',
            'nome'   => 'Treino F — Livre (escolha seu favorito)',
            'grupos' => ['Cardio Academia'],
            'livre'  => true,
        ];
    }

    return $resultado;
}

/**
 * Grupos disponíveis no banco
 */
function getGruposDisponiveis(PDO $pdo): array
{
    $stmt = $pdo->query("
        SELECT DISTINCT grupo_muscular
        FROM exercicios
        WHERE ativo = 1
        ORDER BY grupo_muscular ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Quantidade de exercícios por grupo conforme nível e dias
 * 1-2 dias → 1 por grupo (full body, pouco volume)
 * 3+ dias  → Iniciante:3 | Intermediário:4 | Avançado:5
 */
function getLimiteExercicios(string $nivel, int $dias): int
{
    if ($dias <= 2) return 1;
    return match($nivel) {
        'Avançado'      => 5,
        'Intermediário' => 4,
        default         => 3, // Iniciante
    };
}

// Mantém compatibilidade com chamadas antigas
function getExerciciosPorGrupo(int $dias): int
{
    return $dias <= 2 ? 1 : 3;
}

/**
 * Busca exercícios de um grupo por nível, com limite
 */
function getExerciciosPorNivel(PDO $pdo, string $grupo, int $limite_por_nivel = 3): array
{
    $stmt = $pdo->prepare("
        SELECT id, nome, gif_pasta, gif_arquivo, instrucoes, nivel
        FROM exercicios
        WHERE grupo_muscular = ? AND ativo = 1
        ORDER BY nivel ASC, id ASC
    ");
    $stmt->execute([$grupo]);
    $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $por_nivel = ['Iniciante' => [], 'Intermediário' => [], 'Avançado' => []];
    foreach ($todos as $ex) {
        $n = $ex['nivel'] ?? 'Iniciante';
        if (isset($por_nivel[$n]) && count($por_nivel[$n]) < $limite_por_nivel) {
            $por_nivel[$n][] = $ex;
        }
    }
    return array_filter($por_nivel, fn($exs) => !empty($exs));
}

/**
 * URL correta do GIF com encoding preciso de cada pasta
 */
function getGifUrl(string $pasta, string $arquivo): string
{
    if (empty($arquivo)) return '';

    // Calistenia — GIFs direto na pasta, sem subpasta
    if ($pasta === 'calistenia') {
        return '/gifs/calistenia/' . rawurlencode($arquivo);
    }

    // Alongamento — GIFs direto na pasta, sem subpasta
    if ($pasta === 'alongamento') {
        return '/gifs/alongamento/' . rawurlencode($arquivo);
    }

    // Musculação — subpastas por grupo muscular com encoding específico
    $pasta_encoded = match($pasta) {
        'Bíceps'             => 'B%C3%ADceps',
        'Glúteos'            => 'Gl%C3%BAteos',
        'Tríceps'            => 'Tr%C3%ADceps',
        'Trapézio'           => 'Trap%C3%A9zio',
        'Antebraços'         => 'Antebra%C3%A7os',
        'Eretores da espinha'=> 'Eretores%20da%20espinha',
        'Cardio Academia'    => 'Cardio%20Academia',
        default              => rawurlencode($pasta),
    };

    $arquivo_encoded = rawurlencode($arquivo);
    return '/gifs/musculacao/500%20Gifs%20de%20Muscula%C3%A7%C3%A3o/' . $pasta_encoded . '/' . $arquivo_encoded;
}