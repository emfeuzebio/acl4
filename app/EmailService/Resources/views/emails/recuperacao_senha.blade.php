@component('mail::message')
# Recuperação de Senha

Olá, {{ $nome ?? 'usuário' }}!

Recebemos uma solicitação para redefinir sua senha em **{{ $brand_name }}**.

@component('mail::button', ['url' => $link])
Redefinir Senha
@endcomponent

Este link expira em **{{ $expira_em ?? '1 hora' }}**.

Se você não solicitou isso, nenhuma ação é necessária.

Atenciosamente,  
{{ $brand_name }}
@endcomponent