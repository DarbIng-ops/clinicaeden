@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">📊 Reportes y Estadísticas</h1>
        <p class="text-gray-600 mt-2">Genera reportes detallados del sistema</p>
    </div>

    <!-- Filtros de Reportes -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Filtros de Búsqueda</h2>
        <form method="GET" action="{{ route('admin.reportes') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio', now()->startOfMonth()->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="{{ request('fecha_fin', now()->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    🔍 Generar Reporte
                </button>
            </div>
        </form>
    </div>

    <!-- Tarjetas de Reportes -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Reporte de Pacientes -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">👥 Pacientes</h3>
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">Disponible</span>
            </div>
            <p class="text-gray-600 mb-4">Listado completo de pacientes registrados con sus datos personales</p>
            <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">📥 Descargar PDF →</a>
        </div>

        <!-- Reporte de Consultas -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">🩺 Consultas</h3>
                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">Disponible</span>
            </div>
            <p class="text-gray-600 mb-4">Historial de consultas médicas realizadas en el período</p>
            <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">📥 Descargar PDF →</a>
        </div>

        <!-- Reporte de Hospitalizaciones -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">🏥 Hospitalizaciones</h3>
                <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm">Disponible</span>
            </div>
            <p class="text-gray-600 mb-4">Registro de pacientes hospitalizados y altas médicas</p>
            <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">📥 Descargar PDF →</a>
        </div>

        <!-- Reporte Financiero -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">💰 Reporte Financiero</h3>
                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">Disponible</span>
            </div>
            <p class="text-gray-600 mb-4">Ingresos, facturas pagadas y pendientes del período</p>
            <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">📥 Descargar PDF →</a>
        </div>

        <!-- Reporte de Satisfacción -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">⭐ Satisfacción</h3>
                <span class="bg-pink-100 text-pink-800 px-3 py-1 rounded-full text-sm">Disponible</span>
            </div>
            <p class="text-gray-600 mb-4">Encuestas de satisfacción y calificaciones de pacientes</p>
            <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">📥 Descargar PDF →</a>
        </div>

        <!-- Reporte de Medicamentos -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">💊 Medicamentos</h3>
                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">Disponible</span>
            </div>
            <p class="text-gray-600 mb-4">Inventario de medicamentos y tratamientos recetados</p>
            <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">📥 Descargar PDF →</a>
        </div>

    </div>

    <!-- Estadísticas Rápidas -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-8">
        <h2 class="text-xl font-semibold mb-6">📈 Estadísticas del Mes Actual</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600">{{ \App\Models\Paciente::whereMonth('created_at', now()->month)->count() }}</div>
                <div class="text-gray-600 mt-2">Pacientes Nuevos</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600">{{ \App\Models\Consulta::whereMonth('created_at', now()->month)->count() }}</div>
                <div class="text-gray-600 mt-2">Consultas Realizadas</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600">{{ \App\Models\Hospitalizacion::whereMonth('created_at', now()->month)->count() }}</div>
                <div class="text-gray-600 mt-2">Hospitalizaciones</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-yellow-600">${{ number_format(\App\Models\Factura::where('estado', 'pagado')->whereMonth('created_at', now()->month)->sum('total'), 2) }}</div>
                <div class="text-gray-600 mt-2">Ingresos Totales</div>
            </div>
        </div>
    </div>

</div>
@endsection

