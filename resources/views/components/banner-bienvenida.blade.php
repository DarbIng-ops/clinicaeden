@props(['nombre', 'subtitulo', 'emoji'])

<div class="relative overflow-hidden rounded-2xl mb-6 text-white"
     style="background:linear-gradient(135deg,#1A2E4A 0%,#2D5F8A 100%);">
    {{-- Círculos decorativos (igual que admin ::before / ::after) --}}
    <div class="absolute rounded-full pointer-events-none"
         style="top:-40px;right:-40px;width:200px;height:200px;background:rgba(255,255,255,.06)"></div>
    <div class="absolute rounded-full pointer-events-none"
         style="bottom:-60px;right:60px;width:280px;height:280px;background:rgba(255,255,255,.04)"></div>

    <div class="relative z-10 p-6 md:px-10 md:py-8">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold mb-1">
                    ¡Bienvenido, {{ $nombre }}!
                </h2>
                <p class="text-sm md:text-base mb-1" style="opacity:.9">
                    {{ $subtitulo }} {{ $emoji }}
                </p>
                <p class="text-sm mb-0" style="opacity:.7">
                    <i class="far fa-calendar-alt mr-1"></i>
                    {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                </p>
            </div>
            <div class="hidden md:block flex-shrink-0">
                <img src="{{ asset('images/logoGrande.png') }}" alt="Clínica Eden"
                     style="height:80px;width:auto;opacity:.85;filter:brightness(0) invert(1)"
                     onerror="this.style.display='none'">
            </div>
        </div>
    </div>
</div>
