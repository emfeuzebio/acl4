@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => $brand_url ?? config('app.url')])
            {{ $brand_name ?? config('app.name') }}
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ $brand_name ?? config('app.name') }}.
            {{ $brand_footer ?? '' }}
        @endcomponent
    @endslot
@endcomponent