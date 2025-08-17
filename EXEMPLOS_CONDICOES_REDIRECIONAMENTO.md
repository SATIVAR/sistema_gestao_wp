# 🔀 Exemplos de Condições para Redirecionamento

## ❌ **Condição INCORRETA (que você estava usando):**
```php
if (!$current_user_role === 'super_admin' || !$current_user_role === 'gerente') {
    wp_redirect(home_url());
    exit;
}
```

### Por que não funciona?
- A precedência dos operadores faz com que seja interpretado como: `((!$current_user_role) === 'super_admin') || ((!$current_user_role) === 'gerente')`
- `!$current_user_role` sempre será `false` (se houver role) ou `true` (se não houver)
- `false === 'super_admin'` sempre é `false`
- `true === 'super_admin'` sempre é `false`
- Resultado: a condição sempre será verdadeira e sempre redirecionará

## ✅ **Condições CORRETAS:**

### 1. **Permitir apenas Super Admin OU Gerente:**
```php
// Define o role do usuário atual
$current_user_role = sativar_get_user_role_safe(get_current_user_id());

// Redireciona se NÃO for super_admin E NÃO for gerente
if ($current_user_role !== 'super_admin' && $current_user_role !== 'gerente') {
    wp_safe_redirect(home_url());
    exit;
}
```

### 2. **Permitir apenas Super Admin:**
```php
$current_user_role = sativar_get_user_role_safe(get_current_user_id());

if ($current_user_role !== 'super_admin') {
    wp_safe_redirect(home_url());
    exit;
}
```

### 3. **Permitir apenas Gerente:**
```php
$current_user_role = sativar_get_user_role_safe(get_current_user_id());

if ($current_user_role !== 'gerente') {
    wp_safe_redirect(home_url());
    exit;
}
```

### 4. **Bloquear apenas Atendentes (permitir todos os outros):**
```php
$current_user_role = sativar_get_user_role_safe(get_current_user_id());

if ($current_user_role === 'atendente') {
    wp_safe_redirect(home_url());
    exit;
}
```

### 5. **Permitir Super Admin, Gerente E Atendente (bloquear apenas 'none'):**
```php
$current_user_role = sativar_get_user_role_safe(get_current_user_id());

if ($current_user_role === 'none') {
    wp_safe_redirect(home_url());
    exit;
}
```

### 6. **Usando array para múltiplos roles permitidos:**
```php
$current_user_role = sativar_get_user_role_safe(get_current_user_id());
$roles_permitidos = ['super_admin', 'gerente'];

if (!in_array($current_user_role, $roles_permitidos)) {
    wp_safe_redirect(home_url());
    exit;
}
```

### 7. **Usando função helper personalizada:**
```php
// Adicione esta função no functions-funcao-usuarios.php
function sativar_user_can_access($allowed_roles) {
    $current_user_role = sativar_get_user_role_safe(get_current_user_id());
    
    if (is_string($allowed_roles)) {
        return $current_user_role === $allowed_roles;
    }
    
    if (is_array($allowed_roles)) {
        return in_array($current_user_role, $allowed_roles);
    }
    
    return false;
}

// Uso:
if (!sativar_user_can_access(['super_admin', 'gerente'])) {
    wp_safe_redirect(home_url());
    exit;
}
```

## 🧠 **Lógica das Condições:**

### Operadores Lógicos:
- `&&` (AND): Ambas condições devem ser verdadeiras
- `||` (OR): Pelo menos uma condição deve ser verdadeira
- `!` (NOT): Inverte o valor booleano

### Para PERMITIR acesso:
- Use `&&` quando quiser que TODAS as condições sejam verdadeiras
- Use `||` quando quiser que PELO MENOS UMA condição seja verdadeira

### Para BLOQUEAR acesso:
- Use `!==` para "diferente de"
- Use `===` para "igual a"

## 📝 **Exemplos Práticos:**

### Página só para Super Admin:
```php
$current_user_role = sativar_get_user_role_safe(get_current_user_id());

if ($current_user_role !== 'super_admin') {
    wp_safe_redirect(home_url('/acesso-negado/'));
    exit;
}
```

### Página para Super Admin e Gerente:
```php
$current_user_role = sativar_get_user_role_safe(get_current_user_id());

if ($current_user_role !== 'super_admin' && $current_user_role !== 'gerente') {
    wp_safe_redirect(home_url('/sem-permissao/'));
    exit;
}
```

### Página que bloqueia apenas Atendentes:
```php
$current_user_role = sativar_get_user_role_safe(get_current_user_id());

if ($current_user_role === 'atendente') {
    wp_safe_redirect(home_url('/area-restrita/'));
    exit;
}
```

## 🔍 **Debug das Condições:**

Para testar suas condições, adicione debug temporário:

```php
$current_user_role = sativar_get_user_role_safe(get_current_user_id());

// Debug temporário - remover após teste
if (WP_DEBUG) {
    echo "<!-- DEBUG: Role atual: " . $current_user_role . " -->";
    echo "<!-- DEBUG: Condição (role !== super_admin): " . ($current_user_role !== 'super_admin' ? 'TRUE' : 'FALSE') . " -->";
    echo "<!-- DEBUG: Condição (role !== gerente): " . ($current_user_role !== 'gerente' ? 'TRUE' : 'FALSE') . " -->";
    echo "<!-- DEBUG: Condição final (AND): " . (($current_user_role !== 'super_admin' && $current_user_role !== 'gerente') ? 'TRUE (vai redirecionar)' : 'FALSE (não vai redirecionar)') . " -->";
}

if ($current_user_role !== 'super_admin' && $current_user_role !== 'gerente') {
    wp_safe_redirect(home_url());
    exit;
}
```

## ⚠️ **Dicas Importantes:**

1. **Sempre defina a variável** `$current_user_role` antes de usar
2. **Use `wp_safe_redirect()`** em vez de `wp_redirect()` para maior segurança
3. **Sempre use `exit;`** após redirecionamento
4. **Teste com diferentes tipos de usuário** para garantir que funciona
5. **Use parênteses** para deixar a lógica mais clara: `if (($role !== 'admin') && ($role !== 'manager'))`

## 🎯 **Sua Condição Corrigida:**

```php
// ANTES (incorreto):
if (!$current_user_role === 'super_admin' || !$current_user_role === 'gerente') {

// DEPOIS (correto):
$current_user_role = sativar_get_user_role_safe(get_current_user_id());
if ($current_user_role !== 'super_admin' && $current_user_role !== 'gerente') {
    wp_safe_redirect(home_url());
    exit;
}
```

Agora a condição funcionará corretamente! 🎉