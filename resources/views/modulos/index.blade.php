<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Módulos de Enfermería') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Lista de Módulos</h3>
                        <a href="{{ route('modulos.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Crear Nuevo Módulo
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="space-y-6">
                        @foreach($modulos as $modulo)
                            <div class="border rounded-lg p-6 hover:shadow-lg transition-shadow">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h4 class="text-xl font-semibold text-gray-800">{{ $modulo->nombre }}</h4>
                                        <p class="text-gray-600">
                                            Piso {{ $modulo->piso->numero }} - {{ ucfirst(str_replace('_', ' ', $modulo->tipo)) }}
                                        </p>
                                        @if($modulo->jefeEnfermeria)
                                            <p class="text-sm text-gray-500 mt-1">
                                                Jefe: {{ $modulo->jefeEnfermeria->name }}
                                            </p>
                                        @endif
                                        @if($modulo->descripcion)
                                            <p class="text-gray-600 mt-2">{{ $modulo->descripcion }}</p>
                                        @endif
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('modulos.show', $modulo) }}" class="text-blue-600 hover:text-blue-800">
                                            Ver Detalles
                                        </a>
                                        <a href="{{ route('modulos.edit', $modulo) }}" class="text-green-600 hover:text-green-800">
                                            Editar
                                        </a>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div class="bg-blue-50 p-3 rounded">
                                        <div class="font-medium text-blue-700">Auxiliares</div>
                                        <div class="text-xl font-bold text-blue-800">{{ $modulo->total_auxiliares }}</div>
                                    </div>
                                    <div class="bg-green-50 p-3 rounded">
                                        <div class="font-medium text-green-700">Habitaciones</div>
                                        <div class="text-xl font-bold text-green-800">{{ $modulo->total_habitaciones }}</div>
                                    </div>
                                    <div class="bg-purple-50 p-3 rounded">
                                        <div class="font-medium text-purple-700">Total Camas</div>
                                        <div class="text-xl font-bold text-purple-800">{{ $modulo->total_camas }}</div>
                                    </div>
                                    <div class="bg-orange-50 p-3 rounded">
                                        <div class="font-medium text-orange-700">Disponibles</div>
                                        <div class="text-xl font-bold text-orange-800">{{ $modulo->camas_disponibles }}</div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ route('modulos.show', $modulo) }}" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded text-center block">
                                        Ver Estructura del Módulo
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($modulos->isEmpty())
                        <div class="text-center py-12">
                            <div class="text-gray-500 text-lg">No hay módulos registrados</div>
                            <a href="{{ route('modulos.create') }}" class="mt-4 inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Crear Primer Módulo
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
