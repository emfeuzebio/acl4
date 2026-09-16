@component('mail::message')
# {{ $titulo ?? 'Teste do Microserviço' }}

Olá, {{ $nome ?? 'usuário' }}!

{{ $mensagem ?? 'Este é um e-mail de teste.' }}

@if(!empty($link))
@component('mail::button', ['url' => $link])
{{ $link_texto ?? 'Acessar' }}
@endcomponent
@endif

Atenciosamente,  
{{ $brand_name }}
@endcomponent