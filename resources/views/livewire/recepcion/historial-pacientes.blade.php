<div class="space-y-4">

    {{-- Encabezado --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
            </svg>
            <h3 class="text-sm font-semibold text-gray-700">Base de Pacientes Registrados</h3>
        </div>
        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">
            {{ $pacientes->total() }} registros
        </span>
    </div>

    {{-- Flash success --}}
    @if(session('success'))
    <div class="text-sm px-4 py-2 rounded border"
         style="background:#EBF3FA; border-color:#A8C8E8; color:#1A2E4A">
        {{ session('success') }}
    </div>
    @endif

    {{-- Buscador --}}
    <input wire:model.live.debounce.300ms="buscar"
           type="text"
           placeholder="Buscar por nombre, apellido o DNI..."
           class="w-full border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:ring-1"
           style="focus-ring-color:#2D5F8A">

    {{-- Tabla --}}
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                <tr>
                    <th class="px-4 py-3 text-left">Paciente</th>
                    <th class="px-4 py-3 text-left">DNI</th>
                    <th class="px-4 py-3 text-left">Consultas previas</th>
                    <th class="px-4 py-3 text-left">Última atención</th>
                    <th class="px-4 py-3 text-left">Último egreso</th>
                    <th class="px-4 py-3 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pacientes as $paciente)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">
                            {{ $paciente->nombres }} {{ $paciente->apellidos }}
                        </div>
                        <div class="text-xs text-gray-400">{{ $paciente->email ?? '—' }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $paciente->dni }}</td>
                    <td class="px-4 py-3">
                        <span class="bg-blue-50 text-blue-700 text-xs px-2 py-0.5 rounded-full">
                            {{ $paciente->consultas_count }} consulta(s)
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        {{ $paciente->consultas->first()?->created_at?->format('d/m/Y') ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        {{ $paciente->updated_at->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            {{-- Ver historial clínico --}}
                            <a href="{{ route('recepcion.pacientes.show', $paciente) }}"
                               class="text-xs hover:underline"
                               style="color:#2D5F8A">
                                Ver historial
                            </a>
                            {{-- Reactivar --}}
                            <button wire:click="reactivar({{ $paciente->id }})"
                                    wire:confirm="¿{{ $paciente->nombres }} ya fue atendido {{ $paciente->consultas_count }} vez/veces. ¿Reactivar para nueva consulta?"
                                    class="text-xs text-white px-3 py-1 rounded hover:opacity-90 transition-opacity"
                                    style="background:#2D5F8A">
                                Reactivar
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">
                        No hay pacientes egresados registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $pacientes->links() }}

</div>
