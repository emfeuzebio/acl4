@component('mail::message')
# Convite para acessar {{ $systemName }}

Olá, {{ $name }}!

Você foi convidado a criar sua conta no **{{ $systemName }}**. Clique no botão abaixo para definir sua senha.

@component('mail::button', ['url' => $inviteUrl])
Definir minha senha
@endcomponent

Este link expira **{{ $expiresAt }}**.

Se você não esperava este convite, ignore este e-mail — nenhuma ação é necessária.

Atenciosamente,
Equipe de Administração do {{ $systemName }}
@endcomponent