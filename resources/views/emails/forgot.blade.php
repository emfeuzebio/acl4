
@component('mail::message')
# Recuperação de Senha

Olá, {{ $name ?? 'usuário' }}!

Recebemos uma solicitação para redefinir sua senha.

Clique no botão abaixo para continuar:

@component('mail::button', ['url' => $resetUrl])
Redefinir Senha
@endcomponent

Se você não solicitou isso, nenhuma ação é necessária.

Atenciosamente,  
{{ config('app.name') }}
@endcomponent
