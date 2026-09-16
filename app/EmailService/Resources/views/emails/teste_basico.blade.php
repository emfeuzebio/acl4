@component('mail::message')
# {{ $titulo ?? 'Teste do Microserviço' }}

Olá, {{ $nome ?? 'usuário' }}!

{{ $mensagem ?? 'Este é um e-mail de teste do microserviço ACL4.' }}

@if(!empty($link))
@component('mail::button', ['url' => $link])
{{ $link_texto ?? 'Acessar' }}
@endcomponent
@endif

Se você recebeu este e-mail por engano, pode ignorá-lo.

Atenciosamente,  
{{ config('app.name') }}
@endcomponent