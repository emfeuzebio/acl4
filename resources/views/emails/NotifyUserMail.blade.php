
@component('mail::message')
# Notificação sobre sua Conta de Acesso

Olá, {{ $name ?? 'usuário' }}!

{{ $text }}





Se você não solicitou isso, nenhuma ação é necessária.


Atenciosamente,  
Equipe de Administração do Sistema {{ config('app.name') }}
@endcomponent

