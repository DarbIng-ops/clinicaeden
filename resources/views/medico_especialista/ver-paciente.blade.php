@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Paciente de especialidad</h1>
            <p class="text-gray-600">{{ $paciente->nombre_completo }} · DNI {{ $paciente->dni }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('medico_especialista.pacientes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Lista de pacientes
            </a>
            <a href="{{ route('medico_especialista.pacientes.crear-consulta', $paciente) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-file-medical mr-2"></i>Nueva consulta especializada
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <section class="lg:col-span-2 space-y-6">
            <article class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-microscope text-indigo-600 mr-2"></i>Historial especializado
                </h2>
                @if($paciente->consultas && $paciente->consultas->count())
                    <div class="space-y-4">
                        @foreach($paciente->consultas->where('tipo_consulta', 'especializada')->sortByDesc('fecha_consulta') as $consulta)
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between gap-3">
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-gray-500">{{ $consulta->especialidad ?? 'Especialidad no definida' }}</p>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ Str::limit($consulta->motivo_consulta, 80) }}</h3>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ $consulta->fecha_consulta?->format('d/m/Y H:i') ?? 'Fecha N/D' }}</span>
                                </div>
                                @if($consulta->diagnostico)
                                    <p class="mt-3 text-sm text-gray-700"><span class="font-medium text-gray-900">Diagnóstico:</span> {{ Str::limit($consulta->diagnostico, 160) }}</p>
                                @endif
                                @if($consulta->tratamiento)
                                    <p class="mt-2 text-sm text-gray-700"><span class="font-medium text-gray-900">Tratamiento:</span> {{ Str::limit($consulta->tratamiento, 160) }}</p>
                                @endif
                                {{-- TODO: enlazar a detalle completo de consulta especializada --}}
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <i class="fas fa-notes-medical text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No se registran consultas especializadas con este paciente.</p>
                    </div>
                @endif
            </article>

            <article class="bg-white rounded-lg shadow">
                <header class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-flask text-indigo-600 mr-2"></i>Pruebas y exámenes recientes
                    </h2>
                    {{-- TODO: integrar resultados de laboratorios/imagen --}}
                </header>
                <div class="p-6">
                    <p class="text-sm text-gray-500">Aún no se han cargado resultados de pruebas especializadas.</p>
                </div>
            </article>
        </section>

        <aside class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user text-indigo-600 mr-2"></i>Datos generales
                </h3>
                <dl class="space-y-3 text-sm text-gray-700">
                    <div class="flex justify-between"><dt class="text-gray-500">Edad</dt><dd>{{ $paciente->edad }} años</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Teléfono</dt><dd>{{ $paciente->telefono ?? 'N/D' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Email</dt><dd>{{ $paciente->email ?? 'N/D' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Tipo de sangre</dt>
                        <dd>
                            @if($paciente->tipo_sangre)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                    {{ $paciente->tipo_sangre }}
                                </span>
                            @else
                                <span class="text-gray-400">Sin registrar</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 mb-1">Alergias</dt>
                        <dd>{{ $paciente->alergias ?? 'Sin registrar' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-procedures text-indigo-600 mr-2"></i>Hospitalizaciones</h3>
                @if($paciente->hospitalizaciones && $paciente->hospitalizaciones->count())
                    <ul class="space-y-3 text-sm text-gray-700">
                        @foreach($paciente->hospitalizaciones->sortByDesc('fecha_ingreso') as $hospitalizacion)
                            <li class="border rounded-lg px-4 py-3">
                                <p class="font-medium text-gray-900">Ingreso {{ $hospitalizacion->fecha_ingreso?->format('d/m/Y') ?? 'N/D' }}</p>
                                <p class="text-gray-500 text-xs uppercase">Habitación {{ $hospitalizacion->habitacion->numero ?? 'N/D' }}</p>
                                @if($hospitalizacion->diagnostico_inicial)
                                    <p class="mt-2 text-gray-600">{{ Str::limit($hospitalizacion->diagnostico_inicial, 120) }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500">No se registran hospitalizaciones asociadas.</p>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-clipboard-check text-indigo-600 mr-2"></i>Acciones sugeridas</h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-center"><i class="fas fa-vial text-indigo-500 mr-2"></i>Solicitar exámenes complementarios</li>
                    <li class="flex items-center"><i class="fas fa-share text-indigo-500 mr-2"></i>Coordinar interconsulta</li>
                    <li class="flex items-center"><i class="fas fa-file-signature text-indigo-500 mr-2"></i>Actualizar plan terapéutico</li>
                </ul>
                {{-- TODO: convertir acciones sugeridas en enlaces prácticos --}}
            </div>
        </aside>
    </div>
</div>
@endsection
