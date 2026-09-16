@component('mail::message')
# Recuperação de Senha

Olá, {{ $nome ?? 'usuário' }}!

Recebemos uma solicitação para redefinir sua senha.

@component('mail::button', ['url' => $link])
Redefinir Senha
@endcomponent

Este link expira em **{{ $expira_em ?? '24 horas' }}**.

Se você não solicitou isso, nenhuma ação é necessária.

Atenciosamente,  
{{ config('app.name') }}
@endcomponent