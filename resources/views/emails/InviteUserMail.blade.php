@component('mail::message')
# Convite para acessar {{ config('app.name') }}

Olá, {{ $name }}!

Você foi convidado a criar sua conta no **{{ config('app.name') }}**. Clique no botão abaixo para definir sua senha.

@component('mail::button', ['url' => $inviteUrl])
Definir minha senha
@endcomponent

Este link expira **{{ $expiresAt }}**.

Se você não esperava este convite, ignore este e-mail — nenhuma ação é necessária.

Atenciosamente,
Equipe de Administração do Sistema {{ config('app.name') }}
@endcomponent