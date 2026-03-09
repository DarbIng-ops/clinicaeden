<div class="container mx-auto px-4 py-6">

    {{-- Banner de bienvenida --}}
    <x-banner-bienvenida
        nombre="Cajero/a"
        subtitulo="Gestiona cobros, pagos y el flujo financiero del Sanatorio"
        emoji="💰"
    />

    {{-- Flash success --}}
    @if(session('success'))
        <div class="flex items-center gap-3 rounded-xl px-5 py-3 mb-6 shadow-sm border"
             style="background:#f0fdf4;border-color:#86efac;color:#166534">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    {{-- ══ Grid 2 columnas ══════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 items-start">

        {{-- ══════════════════════════════════════════════════════
             COLUMNA IZQUIERDA — Lista de espera
        ══════════════════════════════════════════════════════ --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">

                {{-- Encabezado --}}
                <div class="flex items-center justify-between px-5 py-4"
                     style="background:linear-gradient(135deg,#1A2E4A 0%,#2D5F8A 100%)">
                    <h3 class="font-bold text-white text-base flex items-center gap-2">
                        <i class="fas fa-users text-sm"></i>
                        Pacientes en Caja
                    </h3>
                    @if($this->pacientesPendientes->count() > 0)
                        <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white rounded-full"
                              style="background:#E67E22">
                            {{ $this->pacientesPendientes->count() }}
                        </span>
                    @endif
                </div>

                {{-- Cards de pacientes --}}
                <div class="divide-y divide-gray-100 max-h-[70vh] overflow-y-auto">

                    @forelse($this->pacientesPendientes as $fac)
                        <div class="p-4 {{ $factura_id === $fac->id ? 'bg-blue-50 border-l-4 border-blue-600' : 'hover:bg-gray-50' }} transition-colors">

                            {{-- Nombre + documento --}}
                            <div class="flex justify-between items-start gap-2 mb-2">
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 text-sm leading-tight">
                                        {{ $fac->paciente?->nombre ?? '' }} {{ $fac->paciente?->apellido ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Doc: {{ $fac->paciente?->numero_documento ?? '—' }}
                                    </p>
                                </div>
                                <span class="flex-shrink-0 text-sm font-bold" style="color:#27AE60">
                                    $ {{ number_format($fac->subtotal, 0, ',', '.') }}
                                </span>
                            </div>

                            {{-- Médico y hora --}}
                            <div class="flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-gray-500 mb-3">
                                @if($fac->consulta?->medico)
                                    <span>
                                        <i class="fas fa-user-md mr-1"></i>
                                        Dr. {{ $fac->consulta->medico->name }}
                                    </span>
                                @elseif($fac->hospitalizacion?->medicoGeneral)
                                    <span>
                                        <i class="fas fa-user-md mr-1"></i>
                                        Dr. {{ $fac->hospitalizacion->medicoGeneral->name }}
                                    </span>
                                @endif
                                <span>
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $fac->created_at->format('H:i') }}
                                </span>
                            </div>

                            {{-- Botón Ver Factura --}}
                            <button
                                wire:click="seleccionarFactura({{ $fac->id }})"
                                class="w-full text-sm font-semibold py-1.5 px-3 rounded-lg transition-colors"
                                style="{{ $factura_id === $fac->id
                                    ? 'background:#2D5F8A;color:#fff'
                                    : 'background:#EFF6FF;color:#1d4ed8' }}">
                                {{ $factura_id === $fac->id ? '✓ Seleccionado' : 'Ver Factura →' }}
                            </button>
                        </div>
                    @empty
                        <div class="py-16 text-center text-gray-400 px-6">
                            <i class="fas fa-inbox text-4xl mb-3 block opacity-30"></i>
                            <p class="font-semibold text-gray-500">Sin pacientes pendientes</p>
                            <p class="text-sm mt-1">Los pacientes derivados a caja aparecerán aquí</p>
                        </div>
                    @endforelse

                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             COLUMNA DERECHA — Factura a cobrar
        ══════════════════════════════════════════════════════ --}}
        <div class="lg:col-span-3">

            @if($this->facturaActiva)
                <div class="bg-white rounded-2xl shadow-md overflow-hidden">

                    {{-- ── Header factura ────────────────────── --}}
                    <div class="px-6 py-4"
                         style="background:linear-gradient(135deg,#1A2E4A 0%,#2D5F8A 100%)">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-base font-bold text-white leading-tight">
                                    {{ $this->facturaActiva->paciente?->nombre ?? '' }}
                                    {{ $this->facturaActiva->paciente?->apellido ?? '' }}
                                </h3>
                                <p class="text-xs mt-0.5" style="color:rgba(255,255,255,.75)">
                                    Doc: {{ $this->facturaActiva->paciente?->numero_documento ?? '—' }}
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-xs font-bold text-white">
                                    {{ $this->facturaActiva->numero_factura }}
                                </p>
                                <p class="text-xs mt-0.5" style="color:rgba(255,255,255,.65)">
                                    {{ $this->facturaActiva->fecha_emision?->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-5">

                        {{-- ── Tabla de servicios (solo lectura) ── --}}
                        <div>
                            <h4 class="font-semibold text-sm mb-3" style="color:#1A2E4A">
                                <i class="fas fa-file-invoice mr-1"></i>
                                Detalle de Servicios
                            </h4>

                            <div class="rounded-xl overflow-hidden border border-gray-100">
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr style="background:#F2F4F7">
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Servicio</th>
                                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase w-14">Cant.</th>
                                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Precio unit.</th>
                                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Subtotal</th>
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
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-gray-800">
                                                    {{ $iconos[$item->tipo] ?? '📌' }}
                                                    {{ $item->concepto }}
                                                </td>
                                                <td class="px-4 py-3 text-center text-gray-600">
                                                    {{ $item->cantidad }}
                                                </td>
                                                <td class="px-4 py-3 text-right text-gray-600">
                                                    @if((float)$item->precio_unitario > 0)
                                                        $ {{ number_format($item->precio_unitario, 0, ',', '.') }}
                                                    @else
                                                        <span class="text-gray-300">—</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-right font-semibold text-gray-800">
                                                    @if((float)$item->subtotal > 0)
                                                        $ {{ number_format($item->subtotal, 0, ',', '.') }}
                                                    @else
                                                        <span class="text-gray-300">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-5 text-center text-gray-400 text-xs">
                                                    Detalle de ítems no disponible — el total es oficial
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- ── Descuento (único campo editable) ── --}}
                        <div class="rounded-xl border border-gray-100 p-4" style="background:#F2F4F7">
                            <h4 class="font-semibold text-sm mb-3" style="color:#1A2E4A">
                                <i class="fas fa-percent mr-1"></i>
                                Descuento
                            </h4>

                            <div class="flex flex-col sm:flex-row gap-3">
                                <div class="sm:w-40">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                        Descuento (%)
                                    </label>
                                    <input
                                        type="number"
                                        wire:model.live="descuento_porcentaje"
                                        min="0" max="100" step="1"
                                        placeholder="0"
                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-2 focus:border-transparent"
                                        style="focus-ring-color:#2D5F8A">
                                    @error('descuento_porcentaje')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                @if($descuento_porcentaje > 0)
                                    <div class="flex-1">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">
                                            Motivo del descuento <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            wire:model.live="motivo_descuento"
                                            placeholder="Ej: Convenio EPS, adulto mayor, discapacidad..."
                                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-2 focus:border-transparent">
                                        @error('motivo_descuento')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                            </div>

                            @if($this->descuentoMonto > 0)
                                <div class="mt-3 text-sm font-semibold text-right" style="color:#C0392B">
                                    - $ {{ number_format($this->descuentoMonto, 0, ',', '.') }} descontados
                                </div>
                            @endif
                        </div>

                        {{-- ── Resumen final ──────────────────── --}}
                        <div class="rounded-xl overflow-hidden border border-gray-200">
                            <div class="divide-y divide-gray-100">
                                <div class="flex justify-between items-center px-5 py-3 text-sm">
                                    <span class="text-gray-500">Subtotal</span>
                                    <span class="font-medium text-gray-800">
                                        $ {{ number_format($this->subtotal, 0, ',', '.') }}
                                    </span>
                                </div>

                                @if($this->descuentoMonto > 0)
                                    <div class="flex justify-between items-center px-5 py-3 text-sm" style="color:#C0392B">
                                        <span>Descuento ({{ number_format($descuento_porcentaje, 0) }}%)</span>
                                        <span class="font-medium">
                                            - $ {{ number_format($this->descuentoMonto, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endif

                                <div class="flex justify-between items-center px-5 py-4" style="background:#F2F4F7">
                                    <span class="text-base font-bold" style="color:#1A2E4A">TOTAL A COBRAR</span>
                                    <span class="text-2xl font-bold" style="color:#27AE60">
                                        $ {{ number_format($this->totalFinal, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- ── Método de pago ─────────────────── --}}
                        <div>
                            <h4 class="font-semibold text-sm mb-3" style="color:#1A2E4A">
                                <i class="fas fa-credit-card mr-1"></i>
                                Método de Pago
                            </h4>

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
                                ] as [$val, $ico, $lbl])
                                    <label class="flex items-center gap-2 cursor-pointer rounded-xl border-2 p-3 transition-all select-none
                                                  {{ $metodo_pago === $val
                                                     ? 'border-blue-500 bg-blue-50'
                                                     : 'border-gray-200 bg-white hover:border-gray-300' }}">
                                        <input
                                            type="radio"
                                            wire:model.live="metodo_pago"
                                            value="{{ $val }}"
                                            class="sr-only">
                                        <span class="text-xl leading-none">{{ $ico }}</span>
                                        <span class="text-xs font-medium text-gray-700 leading-tight">{{ $lbl }}</span>
                                        @if($metodo_pago === $val)
                                            <svg class="w-4 h-4 ml-auto flex-shrink-0 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </label>
                                @endforeach
                            </div>

                            {{-- Calculador de cambio — solo efectivo --}}
                            <div x-show="$wire.metodo_pago === 'efectivo'" x-cloak class="mt-3 space-y-2">
                                <label class="text-xs font-medium text-gray-600">Monto recibido</label>
                                <input type="number"
                                       wire:model.live="monto_recibido"
                                       min="0" step="1000"
                                       class="w-40 border border-gray-300 rounded px-3 py-1.5 text-sm"
                                       placeholder="0">
                                <p class="text-sm font-semibold"
                                   style="color:#2D5F8A"
                                   x-text="'Cambio: $ ' + Math.max(0, parseFloat($wire.monto_recibido || 0) - {{ $this->totalFinal }}).toLocaleString('es-CO')">
                                </p>
                            </div>
                        </div>

                        {{-- ── Botones ────────────────────────── --}}
                        <div class="flex flex-col sm:flex-row gap-3 pt-1">

                            <button
                                wire:click="procesarPago"
                                wire:loading.attr="disabled"
                                {{ !$metodo_pago ? 'disabled' : '' }}
                                class="flex-1 flex items-center justify-center gap-2 py-3 px-6 rounded-xl font-bold text-white transition-all
                                       {{ $metodo_pago ? 'hover:opacity-90 active:scale-[.98]' : 'opacity-50 cursor-not-allowed' }}"
                                style="background:{{ $metodo_pago ? '#27AE60' : '#9CA3AF' }}">
                                <span wire:loading.remove wire:target="procesarPago">
                                    ✓ Procesar Pago
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

                    </div>{{-- /p-6 --}}
                </div>

            @else
                {{-- Estado vacío --}}
                <div class="bg-white rounded-2xl shadow-md flex flex-col items-center justify-center py-24 text-center text-gray-400 px-8">
                    <i class="fas fa-file-invoice-dollar text-6xl mb-4 opacity-20"></i>
                    <p class="text-lg font-semibold text-gray-500">Ningún paciente seleccionado</p>
                    <p class="text-sm mt-2 max-w-xs text-gray-400">
                        Selecciona un paciente de la lista de espera para ver su factura y procesar el cobro.
                    </p>
                </div>
            @endif

        </div>{{-- /col derecha --}}
    </div>{{-- /grid --}}

</div>
