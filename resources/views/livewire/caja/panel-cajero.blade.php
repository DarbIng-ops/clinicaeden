<div class="container mx-auto px-4 py-6">

    {{-- Banner de bienvenida --}}
    <x-banner-bienvenida
        nombre="Cajero/a"
        subtitulo="Gestiona cobros, pagos y el flujo financiero del Sanatorio"
        emoji="💰"
    />

    {{-- Flash success --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-3 mb-6 shadow-sm">
            <svg class="w-5 h-5 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Grid principal: 2 columnas ─────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- ══ COLUMNA IZQUIERDA: Lista de espera ══════════════ --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">

                {{-- Header lista --}}
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between"
                     style="background:linear-gradient(135deg,#1A2E4A 0%,#2D5F8A 60%)">
                    <h3 class="font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Pacientes en Caja
                    </h3>
                    @if($this->pacientesPendientes->count() > 0)
                        <span class="inline-flex items-center justify-center h-6 px-2 text-xs font-bold text-white rounded-full"
                              style="background:#E67E22">
                            {{ $this->pacientesPendientes->count() }}
                        </span>
                    @endif
                </div>

                {{-- Lista de pacientes --}}
                <div class="divide-y divide-gray-100 max-h-[calc(100vh-280px)] overflow-y-auto">
                    @forelse($this->pacientesPendientes as $factura)
                        <button
                            wire:click="seleccionarFactura({{ $factura->id }})"
                            class="w-full text-left px-5 py-4 hover:bg-gray-50 transition-colors
                                   {{ $factura_id === $factura->id ? 'bg-blue-50 border-l-4 border-blue-600' : '' }}">

                            {{-- Nombre + documento --}}
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 truncate">
                                        {{ $factura->paciente?->nombre_completo ?? $factura->paciente?->nombre . ' ' . $factura->paciente?->apellido }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Doc: {{ $factura->paciente?->numero_documento ?? '—' }}
                                    </p>
                                </div>
                                {{-- Subtotal estimado --}}
                                <span class="flex-shrink-0 text-sm font-bold" style="color:#27AE60">
                                    $ {{ number_format($factura->subtotal, 0, ',', '.') }}
                                </span>
                            </div>

                            {{-- Médico y hora --}}
                            <div class="mt-2 flex items-center gap-3 text-xs text-gray-500">
                                @if($factura->consulta?->medico)
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                        </svg>
                                        Dr. {{ $factura->consulta->medico->name }}
                                    </span>
                                @endif
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $factura->created_at->format('H:i') }}
                                </span>
                                <span class="ml-auto font-medium" style="color:#4A90C4">
                                    Ver factura →
                                </span>
                            </div>
                        </button>
                    @empty
                        <div class="py-14 text-center text-gray-400">
                            <svg class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="font-medium">Sin pacientes pendientes</p>
                            <p class="text-sm mt-1">Los pacientes derivados a caja aparecerán aquí</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ══ COLUMNA DERECHA: Factura a cobrar ═══════════════ --}}
        <div class="lg:col-span-3">
            @if($this->facturaActiva)
                <div class="bg-white rounded-2xl shadow-md overflow-hidden">

                    {{-- ── Header factura ──────────────────── --}}
                    <div class="px-6 py-4 border-b border-gray-100"
                         style="background:linear-gradient(135deg,#1A2E4A 0%,#2D5F8A 60%)">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    {{ $this->facturaActiva->paciente?->nombre_completo
                                       ?? ($this->facturaActiva->paciente?->nombre . ' ' . $this->facturaActiva->paciente?->apellido) }}
                                </h3>
                                <p class="text-sm mt-0.5" style="opacity:.8;color:#fff">
                                    Doc: {{ $this->facturaActiva->paciente?->numero_documento ?? '—' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold" style="opacity:.7;color:#fff">
                                    {{ $this->facturaActiva->numero_factura }}
                                </p>
                                <p class="text-xs mt-0.5" style="opacity:.6;color:#fff">
                                    {{ $this->facturaActiva->fecha_emision?->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">

                        {{-- ── Detalle de servicios (solo lectura) ── --}}
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Detalle de Servicios
                            </h4>

                            <div class="overflow-x-auto rounded-xl border border-gray-100">
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr style="background:#F2F4F7">
                                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                                Servicio
                                            </th>
                                            <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide w-16">
                                                Cant.
                                            </th>
                                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                                Precio unit.
                                            </th>
                                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                                Subtotal
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @php
                                            $iconos = [
                                                'consulta'        => '📋',
                                                'procedimiento'   => '🔬',
                                                'tratamiento'     => '💉',
                                                'hospitalizacion' => '🛏',
                                                'medicamento'     => '💊',
                                                'otro'            => '📌',
                                            ];
                                        @endphp

                                        @forelse($this->itemsFactura as $item)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-3 text-gray-800">
                                                    <span class="mr-1">{{ $iconos[$item->tipo] ?? '📌' }}</span>
                                                    {{ $item->concepto }}
                                                </td>
                                                <td class="px-4 py-3 text-center text-gray-600">
                                                    {{ $item->cantidad }}
                                                </td>
                                                <td class="px-4 py-3 text-right text-gray-600">
                                                    @if($item->precio_unitario > 0)
                                                        $ {{ number_format($item->precio_unitario, 0, ',', '.') }}
                                                    @else
                                                        <span class="text-gray-400 text-xs">—</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-right font-medium text-gray-800">
                                                    @if($item->subtotal > 0)
                                                        $ {{ number_format($item->subtotal, 0, ',', '.') }}
                                                    @else
                                                        <span class="text-gray-400 text-xs">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-6 text-center text-gray-400 text-sm">
                                                    Detalle de ítems no disponible
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- ── Descuento ─────────────────────── --}}
                        <div class="rounded-xl border border-gray-100 p-4" style="background:#F2F4F7">
                            <h4 class="font-semibold text-gray-700 mb-3">Descuento</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                        Descuento (%)
                                    </label>
                                    <input type="number"
                                           wire:model.live="descuento_porcentaje"
                                           min="0" max="100" step="0.1"
                                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-2 focus:ring-blue-400"
                                           placeholder="0">
                                    @error('descuento_porcentaje')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Motivo (visible solo si hay descuento) --}}
                                @if($descuento_porcentaje > 0)
                                    <div class="sm:col-span-2" x-data x-cloak>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">
                                            Motivo del descuento <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               wire:model.live="motivo_descuento"
                                               class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-2 focus:ring-blue-400"
                                               placeholder="Ej: Convenio EPS, descuento adulto mayor...">
                                        @error('motivo_descuento')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                            </div>

                            {{-- Monto descontado --}}
                            @if($this->descuentoMonto > 0)
                                <div class="mt-3 flex items-center justify-end gap-2 text-sm font-medium" style="color:#C0392B">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                    Descuento: - $ {{ number_format($this->descuentoMonto, 0, ',', '.') }}
                                </div>
                            @endif
                        </div>

                        {{-- ── Resumen final ─────────────────── --}}
                        <div class="rounded-xl border border-gray-200 overflow-hidden">
                            <div class="divide-y divide-gray-100">
                                <div class="flex justify-between items-center px-5 py-3 text-sm text-gray-600">
                                    <span>Subtotal</span>
                                    <span class="font-medium">$ {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                                </div>
                                @if($this->descuentoMonto > 0)
                                    <div class="flex justify-between items-center px-5 py-3 text-sm" style="color:#C0392B">
                                        <span>Descuento ({{ $descuento_porcentaje }}%)</span>
                                        <span class="font-medium">- $ {{ number_format($this->descuentoMonto, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between items-center px-5 py-4" style="background:#F2F4F7">
                                    <span class="text-lg font-bold" style="color:#1A2E4A">TOTAL A COBRAR</span>
                                    <span class="text-2xl font-bold" style="color:#27AE60">
                                        $ {{ number_format($this->totalFinal, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- ── Método de pago ────────────────── --}}
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-3">Método de Pago</h4>
                            @error('metodo_pago')
                                <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
                            @enderror

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @foreach([
                                    ['efectivo',        '💵', 'Efectivo'],
                                    ['tarjeta_debito',  '💳', 'Tarjeta débito'],
                                    ['tarjeta_credito', '💳', 'Tarjeta crédito'],
                                    ['transferencia',   '🏦', 'Transferencia'],
                                    ['eps_convenio',    '🏥', 'EPS / Convenio'],
                                ] as [$valor, $icono, $etiqueta])
                                    <label class="flex items-center gap-2 cursor-pointer rounded-xl border-2 p-3 transition-all
                                                  {{ $metodo_pago === $valor
                                                     ? 'border-blue-500 bg-blue-50'
                                                     : 'border-gray-200 hover:border-gray-300 bg-white' }}">
                                        <input type="radio"
                                               wire:model.live="metodo_pago"
                                               value="{{ $valor }}"
                                               class="sr-only">
                                        <span class="text-lg">{{ $icono }}</span>
                                        <span class="text-xs font-medium text-gray-700">{{ $etiqueta }}</span>
                                        @if($metodo_pago === $valor)
                                            <svg class="w-4 h-4 ml-auto text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- ── Botones ───────────────────────── --}}
                        <div class="flex flex-col sm:flex-row gap-3 pt-2">
                            <button
                                wire:click="procesarPago"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-70 cursor-not-allowed"
                                @disabled(!$metodo_pago)
                                class="flex-1 flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-bold text-white transition-all
                                       {{ $metodo_pago
                                          ? 'hover:opacity-90 active:scale-95'
                                          : 'opacity-50 cursor-not-allowed' }}"
                                style="background:{{ $metodo_pago ? '#27AE60' : '#9CA3AF' }}">
                                <span wire:loading.remove wire:target="procesarPago">
                                    <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Procesar Pago
                                </span>
                                <span wire:loading wire:target="procesarPago" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    Procesando...
                                </span>
                            </button>

                            <button
                                wire:click="cancelarSeleccion"
                                class="px-6 py-3 rounded-xl font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                                Cancelar
                            </button>
                        </div>

                    </div>{{-- end p-6 --}}
                </div>

            @else
                {{-- Estado vacío --}}
                <div class="bg-white rounded-2xl shadow-md flex flex-col items-center justify-center py-24 px-8 text-center text-gray-400">
                    <svg class="w-20 h-20 mb-5 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-lg font-semibold text-gray-500">Ningún paciente seleccionado</p>
                    <p class="text-sm mt-2 max-w-xs">
                        Selecciona un paciente de la lista de espera para ver su factura y procesar el cobro.
                    </p>
                </div>
            @endif
        </div>
    </div>{{-- end grid --}}

</div>
