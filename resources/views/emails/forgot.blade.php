@component('mail::message', ['systemName' => $systemName ?? config('app.name')])
# Recuperação de Senha

Olá, {{ $name ?? 'usuário' }}!

Recebemos uma solicitação para redefinir sua senha.

Clique no botão abaixo para continuar:

@component('mail::button', ['url' => $resetUrl])
Redefinir Senha
@endcomponent

Se você não solicitou isso, nenhuma ação é necessária.

Atenciosamente,
Equipe de Administração do {{ $systemName ?? config('app.name') }}
@endcomponent