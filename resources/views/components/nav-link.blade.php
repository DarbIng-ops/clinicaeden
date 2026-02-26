@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-eden-azul-claro text-sm font-medium leading-5 text-white focus:outline-none focus:border-eden-azul-claro transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-100 hover:text-eden-azul-claro hover:border-eden-azul-claro focus:outline-none focus:text-eden-azul-claro focus:border-eden-azul-claro transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
