@props(['nombre', 'subtitulo', 'emoji'])

{{-- ── Header motivacional (pixel-perfect igual al banner Admin) ───────── --}}
<div class="relative overflow-hidden text-white"
     style="background:linear-gradient(135deg,#1A2E4A 0%,#2D5F8A 100%);border-radius:12px;padding:2rem 2.5rem;margin-bottom:1.5rem;">
    {{-- Círculo decorativo ::before --}}
    <div class="absolute rounded-full"
         style="top:-40px;right:-40px;width:200px;height:200px;background:rgba(255,255,255,.06)"></div>
    {{-- Círculo decorativo ::after --}}
    <div class="absolute rounded-full"
         style="bottom:-60px;right:60px;width:280px;height:280px;background:rgba(255,255,255,.04)"></div>

    <div class="flex justify-between items-center">
        <div style="position:relative;z-index:1;">
            <h2 class="font-bold mb-1" style="font-size:1.8rem;">
                ¡Bienvenido, {{ $nombre }}!
            </h2>
            <p class="mb-1" style="font-size:1rem;opacity:.9;">
                {{ $subtitulo }} {{ $emoji }}
            </p>
            <p class="mb-0" style="font-size:.85rem;opacity:.7;">
                <i class="far fa-calendar-alt mr-1"></i>
                {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
            </p>
        </div>
        <div class="hidden md:block" style="position:relative;z-index:1;">
            <img src="{{ asset(config('clinica.logo')) }}"
                 alt="{{ config('clinica.nombre_largo') }}"
                 style="height:80px;width:auto;opacity:.85;filter:brightness(0) invert(1)"
                 onerror="this.style.display='none'">
        </div>
    </div>
</div>
