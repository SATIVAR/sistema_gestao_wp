<?php

function get_total_associados() {
    $users = get_users(array('role' => 'associados'));
    if (empty($users)) {
        $users = get_users(array('role' => 'subscriber'));
    }
    return count($users);
}

function get_novos_associados_mes() {
    $args = array(
        'role' => 'associados',
        'date_query' => array(
            array(
                'after' => '1 month ago',
            ),
        ),
    );
    $users = get_users($args);
    if (empty($users)) {
        $args['role'] = 'subscriber';
        $users = get_users($args);
    }
    return count($users);
}

function get_associados_ativos() {
    $users = get_users(array('role' => 'associados'));
    if (empty($users)) {
        $users = get_users(array('role' => 'subscriber'));
    }
    
    $ativos = 0;
    foreach ($users as $user) {
        $associado_ativo = get_field('field_66b252b04990d', 'user_' . $user->ID);
        if ($associado_ativo === true || $associado_ativo === '1' || $associado_ativo === 1) {
            $ativos++;
        }
    }
    
    return $ativos;
}

function get_associados_por_estado() {
    $users = get_users(array('role' => 'associados'));
    if (empty($users)) {
        $users = get_users(array('role' => 'subscriber'));
    }
    
    $estados = array();
    
    foreach ($users as $user) {
        $estado = get_field('field_66db0b0e8561e', 'user_' . $user->ID);
        if ($estado) {
            $estado_nome = get_estado_nome($estado);
            if (!isset($estados[$estado_nome])) {
                $estados[$estado_nome] = 0;
            }
            $estados[$estado_nome]++;
        }
    }
    
    if (empty($estados)) {
        return array(
            'labels' => ['São Paulo', 'Rio de Janeiro', 'Minas Gerais'],
            'data' => [1, 1, 1],
        );
    }
    
    arsort($estados);
    $top_estados = array_slice($estados, 0, 6, true);
    
    return array(
        'labels' => array_keys($top_estados),
        'data' => array_values($top_estados),
    );
}

function get_crescimento_associados() {
    $meses = array();
    for ($i = 11; $i >= 0; $i--) {
        $mes_nome = date('M', strtotime("-$i months"));
        
        $args = array(
            'role' => 'associados',
            'date_query' => array(
                array(
                    'year' => date('Y', strtotime("-$i months")),
                    'month' => date('n', strtotime("-$i months")),
                ),
            ),
        );
        $users = get_users($args);
        if (empty($users)) {
            $args['role'] = 'subscriber';
            $users = get_users($args);
        }
        $meses[$mes_nome] = count($users);
    }
    
    return array(
        'labels' => array_keys($meses),
        'data' => array_values($meses),
    );
}

function get_associados_por_tipo() {
    $users = get_users(array('role' => 'associados'));
    if (empty($users)) {
        $users = get_users(array('role' => 'subscriber'));
    }
    
    $tipos = array(
        'assoc_paciente' => 0,
        'assoc_respon' => 0,
        'assoc_tutor' => 0,
        'assoc_colab' => 0
    );
    
    foreach ($users as $user) {
        $tipo = get_field('field_66b40ca7a5636', 'user_' . $user->ID);
        if ($tipo && isset($tipos[$tipo])) {
            $tipos[$tipo]++;
        } else {
            $tipos['assoc_paciente']++;
        }
    }
    
    $labels = array(
        'assoc_paciente' => 'Paciente',
        'assoc_respon' => 'Responsável',
        'assoc_tutor' => 'Tutor de Animal',
        'assoc_colab' => 'Colaborador'
    );
    
    return array(
        'labels' => array_values($labels),
        'data' => array_values($tipos),
    );
}

function get_associados_por_genero() {
    $users = get_users(array('role' => 'associados'));
    if (empty($users)) {
        $users = get_users(array('role' => 'subscriber'));
    }
    
    $generos = array(
        'masculino' => 0,
        'feminino' => 0,
        'naobinario' => 0,
        'naoinformar' => 0
    );
    
    foreach ($users as $user) {
        $genero = get_field('field_66db09e620957', 'user_' . $user->ID);
        
        // Debug para verificar o valor retornado
        error_log('Genero para user ' . $user->ID . ': ' . var_export($genero, true));
        
        // Normalizar o valor do gênero
        if ($genero) {
            $genero = strtolower(trim($genero));
            
            // Mapear possíveis variações
            switch ($genero) {
                case 'masculino':
                case 'masc':
                case 'm':
                    $generos['masculino']++;
                    break;
                case 'feminino':
                case 'feminio': // mantendo a grafia original caso exista
                case 'fem':
                case 'f':
                    $generos['feminino']++;
                    break;
                case 'naobinario':
                case 'não-binário':
                case 'nao-binario':
                case 'nb':
                    $generos['naobinario']++;
                    break;
                case 'naoinformar':
                case 'não informar':
                case 'nao informar':
                case 'prefiro não informar':
                case 'prefiro nao informar':
                    $generos['naoinformar']++;
                    break;
                default:
                    $generos['naoinformar']++;
                    break;
            }
        } else {
            $generos['naoinformar']++;
        }
    }
    
    $labels = array(
        'masculino' => 'Masculino',
        'feminino' => 'Feminino',
        'naobinario' => 'Não-Binário',
        'naoinformar' => 'Não Informado'
    );
    
    return array(
        'labels' => array_values($labels),
        'data' => array_values($generos),
    );
}

function get_associados_com_plano_saude() {
    $users = get_users(array('role' => 'associados'));
    if (empty($users)) {
        $users = get_users(array('role' => 'subscriber'));
    }
    
    $planos = array('sim' => 0, 'nao' => 0);
    
    foreach ($users as $user) {
        $tem_plano = get_field('field_66db0a604696d', 'user_' . $user->ID);
        if ($tem_plano && isset($planos[$tem_plano])) {
            $planos[$tem_plano]++;
        } else {
            $planos['nao']++;
        }
    }
    
    return array(
        'labels' => ['Com Plano', 'Sem Plano'],
        'data' => [$planos['sim'], $planos['nao']],
    );
}

function get_associados_ativos_por_estado() {
    $users = get_users(array('role' => 'associados'));
    if (empty($users)) {
        $users = get_users(array('role' => 'subscriber'));
    }
    
    $estados = array();
    $total_ativos = 0;
    
    foreach ($users as $user) {
        // Verificar se o associado está ativo usando o campo correto
        $associado_ativo = get_field('field_66b252b04990d', 'user_' . $user->ID);
        
        // Debug para verificar o valor
        error_log('User ID: ' . $user->ID . ' - Associado Ativo: ' . var_export($associado_ativo, true));
        
        // Verificar diferentes formatos possíveis do campo boolean
        $is_ativo = false;
        if ($associado_ativo === true || $associado_ativo === 1 || $associado_ativo === '1' || 
            $associado_ativo === 'true' || $associado_ativo === 'yes' || $associado_ativo === 'sim') {
            $is_ativo = true;
        }
        
        if ($is_ativo) {
            $total_ativos++;
            // Pegar o estado do associado ativo
            $estado = get_field('field_66db0b0e8561e', 'user_' . $user->ID);
            
            if ($estado) {
                $estado_nome = get_estado_nome($estado);
                if (!isset($estados[$estado_nome])) {
                    $estados[$estado_nome] = 0;
                }
                $estados[$estado_nome]++;
            } else {
                // Se não tem estado definido, adicionar como "Não Informado"
                if (!isset($estados['Não Informado'])) {
                    $estados['Não Informado'] = 0;
                }
                $estados['Não Informado']++;
            }
        }
    }
    
    // Log para debug
    error_log('Total de associados ativos encontrados: ' . $total_ativos);
    error_log('Estados encontrados: ' . print_r($estados, true));
    
    if (empty($estados)) {
        return array(
            'labels' => ['São Paulo', 'Rio de Janeiro', 'Minas Gerais'],
            'data' => [0, 0, 0],
            'total_ativos' => 0
        );
    }
    
    // Ordenar por quantidade (maior para menor)
    arsort($estados);
    
    // Pegar os top 8 estados para melhor visualização
    $top_estados = array_slice($estados, 0, 8, true);
    
    return array(
        'labels' => array_keys($top_estados),
        'data' => array_values($top_estados),
        'total_ativos' => $total_ativos
    );
}

function get_associados_com_medico_prescritor() {
    $users = get_users(array('role' => 'associados'));
    if (empty($users)) {
        $users = get_users(array('role' => 'subscriber'));
    }
    
    $com_medico = 0;
    $sem_medico = 0;
    
    foreach ($users as $user) {
        $tem_medico = get_field('field_66db34cd0310a', 'user_' . $user->ID);
        if ($tem_medico === 'sim') {
            $com_medico++;
        } else {
            $sem_medico++;
        }
    }
    
    return array(
        'labels' => ['Com Médico Prescritor', 'Sem Médico Prescritor'],
        'data' => [$com_medico, $sem_medico],
    );
}

function get_historico_uso_cannabis() {
    $users = get_users(array('role' => 'associados'));
    if (empty($users)) {
        $users = get_users(array('role' => 'subscriber'));
    }
    
    $uso_cannabis = array(
        'sim' => 0,
        'nao' => 0,
        'nao_informado' => 0
    );
    
    foreach ($users as $user) {
        $fez_uso = get_field('field_66db34a5824c8', 'user_' . $user->ID);
        
        if ($fez_uso) {
            $fez_uso = strtolower(trim($fez_uso));
            switch ($fez_uso) {
                case 'sim':
                case 'yes':
                case '1':
                    $uso_cannabis['sim']++;
                    break;
                case 'nao':
                case 'não':
                case 'no':
                case '0':
                    $uso_cannabis['nao']++;
                    break;
                default:
                    $uso_cannabis['nao_informado']++;
                    break;
            }
        } else {
            $uso_cannabis['nao_informado']++;
        }
    }
    
    return array(
        'labels' => ['Já Usou Cannabis', 'Nunca Usou', 'Não Informado'],
        'data' => [$uso_cannabis['sim'], $uso_cannabis['nao'], $uso_cannabis['nao_informado']],
    );
}

function get_estado_nome($sigla) {
    $estados = array(
        'ac' => 'Acre',
        'al' => 'Alagoas',
        'ap' => 'Amapá',
        'am' => 'Amazonas',
        'ba' => 'Bahia',
        'ce' => 'Ceará',
        'df' => 'Distrito Federal',
        'es' => 'Espírito Santo',
        'go' => 'Goiás',
        'ma' => 'Maranhão',
        'mt' => 'Mato Grosso',
        'ms' => 'Mato Grosso do Sul',
        'mg' => 'Minas Gerais',
        'pa' => 'Pará',
        'pb' => 'Paraíba',
        'pr' => 'Paraná',
        'pe' => 'Pernambuco',
        'pi' => 'Piauí',
        'rj' => 'Rio de Janeiro',
        'rn' => 'Rio Grande do Norte',
        'rs' => 'Rio Grande do Sul',
        'ro' => 'Rondônia',
        'rr' => 'Roraima',
        'sc' => 'Santa Catarina',
        'sp' => 'São Paulo',
        'se' => 'Sergipe',
        'to' => 'Tocantins'
    );
    
    return isset($estados[$sigla]) ? $estados[$sigla] : $sigla;
}

add_action('rest_api_init', function () {
    register_rest_route('associados/v1', '/stats', array(
        'methods' => 'GET',
        'callback' => 'get_associados_stats',
        'permission_callback' => function () {
            return current_user_can('administrator') || current_user_can('manage_options');
        }
    ));
});

function get_associados_stats() {
    try {
        $stats = array(
            'total' => get_total_associados(),
            'novos_mes' => get_novos_associados_mes(),
            'ativos' => get_associados_ativos(),
            'por_estado' => get_associados_por_estado(),
            'crescimento' => get_crescimento_associados(),
            'por_tipo' => get_associados_por_tipo(),
            'por_genero' => get_associados_por_genero(),
            'com_plano_saude' => get_associados_com_plano_saude(),
            'ativos_por_estado' => get_associados_ativos_por_estado(),
            'com_medico_prescritor' => get_associados_com_medico_prescritor(),
            'historico_uso_cannabis' => get_historico_uso_cannabis(),
        );

        return new WP_REST_Response($stats, 200);
    } catch (Exception $e) {
        return new WP_REST_Response(array('error' => $e->getMessage()), 500);
    }
}