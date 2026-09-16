@component('mail::message')
# Senha Alterada

Olá, {{ $nome ?? 'usuário' }}!

Sua senha no **{{ $brand_name ?? config('app.name') }}** foi alterada com sucesso.

**Detalhes:**
- Data/hora: {{ $data ?? now()->format('d/m/Y H:i') }}
- IP de origem: {{ $ip ?? 'desconhecido' }}

Se foi você, nenhuma ação é necessária.

**Se você NÃO reconhece esta alteração**, entre em contato imediatamente com o suporte.

Atenciosamente,  
{{ $brand_name ?? config('app.name') }}
@endcomponent