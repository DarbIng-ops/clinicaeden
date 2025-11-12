@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Estadísticas de especialidad</h1>
            <p class="text-gray-600">Visión general del desempeño clínico y carga de trabajo.</p>
        </div>
        <a href="{{ route('medico_especialista.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
            <i class="fas fa-tachometer-alt mr-2"></i>Volver al dashboard
        </a>
    </div>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <article class="bg-white rounded-lg shadow p-6">
            <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Consultas este mes</h2>
            <p class="mt-4 text-4xl font-bold text-indigo-600">{{ $estadisticas['total_consultas_mes'] ?? 0 }}</p>
            <p class="mt-2 text-sm text-gray-500">Total de consultas especializadas registradas durante el mes en curso.</p>
        </article>

        <article class="bg-white rounded-lg shadow p-6">
            <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Pacientes atendidos</h2>
            <p class="mt-4 text-4xl font-bold text-indigo-600">{{ $estadisticas['pacientes_atendidos'] ?? 0 }}</p>
            <p class="mt-2 text-sm text-gray-500">Pacientes únicos con al menos una consulta especializada.</p>
        </article>

        <article class="bg-white rounded-lg shadow p-6">
            <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Consultas pendientes</h2>
            <p class="mt-4 text-4xl font-bold text-red-600">{{ $estadisticas['consultas_pendientes'] ?? 0 }}</p>
            <p class="mt-2 text-sm text-gray-500">Casos que requieren seguimiento o cierre de atención.</p>
        </article>
    </section>

    <section class="mt-10 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <article class="bg-white rounded-lg shadow p-6">
            <header class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center"><i class="fas fa-calendar-check text-indigo-600 mr-2"></i>Agenda próxima</h2>
                {{-- TODO: integrar agenda real de consultas --}}
            </header>
            <p class="text-sm text-gray-500">Próximamente se mostrarán consultas programadas y prioridades clínicas.</p>
        </article>

        <article class="bg-white rounded-lg shadow p-6">
            <header class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center"><i class="fas fa-chart-line text-indigo-600 mr-2"></i>Indicadores clave</h2>
                {{-- TODO: agregar métricas específicas por especialidad --}}
            </header>
            <ul class="space-y-3 text-sm text-gray-700">
                <li class="flex justify-between"><span>Tasa de continuidad de tratamiento</span><span class="text-gray-500">Pendiente</span></li>
                <li class="flex justify-between"><span>Promedio de consultas por paciente</span><span class="text-gray-500">Pendiente</span></li>
                <li class="flex justify-between"><span>Interconsultas coordinadas</span><span class="text-gray-500">Pendiente</span></li>
            </ul>
        </article>
    </section>
</div>
@endsection
