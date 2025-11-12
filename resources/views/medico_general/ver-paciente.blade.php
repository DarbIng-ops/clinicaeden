@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Paciente: {{ $paciente->nombre_completo }}</h1>
            <p class="text-gray-600">DNI {{ $paciente->dni }} · {{ $paciente->edad }} años</p>
        </div>
        <div class="space-x-2">
            <a href="{{ route('medico_general.pacientes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Volver a mis pacientes
            </a>
            {{-- TODO: acciones para evolución clínica o notas rápidas --}}
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <section class="lg:col-span-2 space-y-6">
            <article class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-heartbeat text-blue-600 mr-2"></i>Resumen clínico
                </h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <div>
                        <dt class="font-medium text-gray-500 uppercase tracking-wide">Tipo de sangre</dt>
                        <dd class="mt-1">
                            @if($paciente->tipo_sangre)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                    {{ $paciente->tipo_sangre }}
                                </span>
                            @else
                                <span class="text-gray-400">Sin registrar</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500 uppercase tracking-wide">Alergias</dt>
                        <dd class="mt-1">{{ $paciente->alergias ?? 'Sin registrar' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500 uppercase tracking-wide">Enfermedades crónicas</dt>
                        <dd class="mt-1">{{ $paciente->enfermedades_cronicas ?? 'Sin registrar' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500 uppercase tracking-wide">Última actualización</dt>
                        <dd class="mt-1">{{ optional($paciente->updated_at)->diffForHumans() ?? 'N/D' }}</dd>
                    </div>
                </dl>
            </article>

            <article class="bg-white rounded-lg shadow">
                <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-file-medical text-blue-600 mr-2"></i>Historial de consultas
                    </h2>
                    <a href="{{ route('medico_general.pacientes.crear-consulta', $paciente) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                        <i class="fas fa-plus mr-2"></i>Nueva consulta
                    </a>
                </div>
                <div class="p-6">
                    @if($paciente->consultas && $paciente->consultas->count())
                        <div class="space-y-4">
                            @foreach($paciente->consultas->sortByDesc('fecha_consulta') as $consulta)
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm text-gray-500">{{ $consulta->fecha_consulta?->format('d/m/Y H:i') ?? 'Fecha no registrada' }}</p>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ Str::limit($consulta->motivo_consulta, 80) }}</h3>
                                        </div>
                                        @if($consulta->estado)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                {{ ucfirst($consulta->estado) }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($consulta->diagnostico)
                                        <p class="mt-3 text-sm text-gray-700">
                                            <span class="font-medium text-gray-900">Diagnóstico:</span>
                                            {{ Str::limit($consulta->diagnostico, 140) }}
                                        </p>
                                    @endif
                                    {{-- TODO: enlazar a vista detallada de consulta cuando exista --}}
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10">
                            <i class="fas fa-stethoscope text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Este paciente aún no tiene consultas registradas contigo.</p>
                        </div>
                    @endif
                </div>
            </article>

            <article class="bg-white rounded-lg shadow">
                <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-hospital-user text-blue-600 mr-2"></i>Hospitalizaciones recientes
                    </h2>
                    <a href="{{ route('medico_general.hospitalizaciones.crear') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-lg transition">
                        <i class="fas fa-plus mr-2"></i>Hospitalizar
                    </a>
                </div>
                <div class="p-6">
                    @if($paciente->hospitalizaciones && $paciente->hospitalizaciones->count())
                        <ul class="space-y-4">
                            @foreach($paciente->hospitalizaciones->sortByDesc('fecha_ingreso') as $hospitalizacion)
                                <li class="border rounded-lg p-4">
                                    <header class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm text-gray-500">Ingreso {{ $hospitalizacion->fecha_ingreso?->format('d/m/Y H:i') ?? 'N/D' }}</p>
                                            <h3 class="text-base font-semibold text-gray-900">Habitación {{ $hospitalizacion->habitacion->numero ?? 'Sin asignar' }}</h3>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                            {{ ucfirst($hospitalizacion->estado ?? 'pendiente') }}
                                        </span>
                                    </header>
                                    @if($hospitalizacion->diagnostico_inicial)
                                        <p class="mt-3 text-sm text-gray-700">
                                            <span class="font-medium text-gray-900">Diagnóstico:</span>
                                            {{ Str::limit($hospitalizacion->diagnostico_inicial, 140) }}
                                        </p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-10">
                            <i class="fas fa-hospital text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">No se registran hospitalizaciones asociadas a este paciente.</p>
                        </div>
                    @endif
                </div>
            </article>
        </section>

        <aside class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>Datos personales
                </h2>
                <ul class="space-y-3 text-sm text-gray-700">
                    <li class="flex justify-between"><span class="text-gray-500">Teléfono:</span><span>{{ $paciente->telefono ?? 'N/D' }}</span></li>
                    <li class="flex justify-between"><span class="text-gray-500">Email:</span><span>{{ $paciente->email ?? 'N/D' }}</span></li>
                    <li class="flex justify-between"><span class="text-gray-500">Dirección:</span><span class="text-right">{{ $paciente->direccion ?? 'N/D' }}</span></li>
                    <li class="flex justify-between"><span class="text-gray-500">Ciudad:</span><span>{{ $paciente->ciudad ?? 'N/D' }}</span></li>
                </ul>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user-md text-blue-600 mr-2"></i>Acciones rápidas</h2>
                <div class="space-y-2">
                    @if($paciente->consultas && $paciente->consultas->where('estado', 'pendiente')->isNotEmpty())
                        @php($consultaPendiente = $paciente->consultas->where('estado', 'pendiente')->first())
                        <a href="{{ route('medico_general.consultas.atender', ['consulta' => $consultaPendiente->id]) }}"
                           class="block w-full text-center px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition">
                            Atender consulta pendiente
                        </a>
                    @else
                        <span class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-500 rounded-lg">
                            Sin consultas pendientes
                        </span>
                    @endif
                    {{-- TODO: Añadir acciones adicionales (crear nota, solicitar exámenes, etc.) --}}
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
