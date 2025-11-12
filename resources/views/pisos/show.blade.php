<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Piso') }} {{ $piso->numero }} - {{ $piso->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800">{{ $piso->nombre }}</h3>
                                @if($piso->descripcion)
                                    <p class="text-gray-600 mt-2">{{ $piso->descripcion }}</p>
                                @endif
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('pisos.edit', $piso) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Editar Piso
                                </a>
                                <a href="{{ route('pisos.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Volver
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas del Piso -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-blue-600 font-semibold">Consultorios</div>
                            <div class="text-2xl font-bold text-blue-800">{{ $piso->total_consultorios }}</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-green-600 font-semibold">Módulos</div>
                            <div class="text-2xl font-bold text-green-800">{{ $piso->total_modulos }}</div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="text-purple-600 font-semibold">Habitaciones</div>
                            <div class="text-2xl font-bold text-purple-800">{{ $piso->total_habitaciones }}</div>
                        </div>
                        <div class="bg-orange-50 p-4 rounded-lg">
                            <div class="text-orange-600 font-semibold">Total Camas</div>
                            <div class="text-2xl font-bold text-orange-800">{{ $piso->total_camas }}</div>
                        </div>
                    </div>

                    <!-- Consultorios -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-xl font-semibold text-gray-800">Consultorios</h4>
                            <a href="{{ route('consultorios.create', ['piso_id' => $piso->id]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Agregar Consultorio
                            </a>
                        </div>

                        @if($piso->consultorios->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach($piso->consultorios as $consultorio)
                                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex justify-between items-start mb-2">
                                            <h5 class="font-semibold text-gray-800">{{ $consultorio->numero }} - {{ $consultorio->nombre }}</h5>
                                            <span class="px-2 py-1 text-xs rounded-full {{ $consultorio->disponible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $consultorio->disponible ? 'Disponible' : 'Ocupado' }}
                                            </span>
                                        </div>
                                        @if($consultorio->descripcion)
                                            <p class="text-sm text-gray-600 mb-2">{{ $consultorio->descripcion }}</p>
                                        @endif
                                        <div class="flex space-x-2">
                                            <a href="{{ route('consultorios.show', $consultorio) }}" class="text-blue-600 hover:text-blue-800 text-sm">Ver</a>
                                            <a href="{{ route('consultorios.edit', $consultorio) }}" class="text-green-600 hover:text-green-800 text-sm">Editar</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 bg-gray-50 rounded-lg">
                                <p class="text-gray-500">No hay consultorios en este piso</p>
                                <a href="{{ route('consultorios.create', ['piso_id' => $piso->id]) }}" class="mt-2 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Agregar Primer Consultorio
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Módulos de Enfermería -->
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-xl font-semibold text-gray-800">Módulos de Enfermería</h4>
                            <a href="{{ route('modulos.create', ['piso_id' => $piso->id]) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Agregar Módulo
                            </a>
                        </div>

                        @if($piso->modulosEnfermeria->count() > 0)
                            <div class="space-y-4">
                                @foreach($piso->modulosEnfermeria as $modulo)
                                    <div class="border rounded-lg p-6 hover:shadow-md transition-shadow">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <h5 class="text-lg font-semibold text-gray-800">{{ $modulo->nombre }}</h5>
                                                <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $modulo->tipo)) }}</p>
                                                @if($modulo->jefeEnfermeria)
                                                    <p class="text-sm text-gray-500 mt-1">
                                                        Jefe: {{ $modulo->jefeEnfermeria->name }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="flex space-x-2">
                                                <a href="{{ route('modulos.show', $modulo) }}" class="text-blue-600 hover:text-blue-800">Ver</a>
                                                <a href="{{ route('modulos.edit', $modulo) }}" class="text-green-600 hover:text-green-800">Editar</a>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                            <div class="bg-gray-50 p-3 rounded">
                                                <div class="font-medium text-gray-700">Auxiliares</div>
                                                <div class="text-lg font-bold text-blue-600">{{ $modulo->total_auxiliares }}</div>
                                            </div>
                                            <div class="bg-gray-50 p-3 rounded">
                                                <div class="font-medium text-gray-700">Habitaciones</div>
                                                <div class="text-lg font-bold text-green-600">{{ $modulo->total_habitaciones }}</div>
                                            </div>
                                            <div class="bg-gray-50 p-3 rounded">
                                                <div class="font-medium text-gray-700">Total Camas</div>
                                                <div class="text-lg font-bold text-purple-600">{{ $modulo->total_camas }}</div>
                                            </div>
                                            <div class="bg-gray-50 p-3 rounded">
                                                <div class="font-medium text-gray-700">Camas Ocupadas</div>
                                                <div class="text-lg font-bold text-orange-600">{{ $modulo->camas_ocupadas }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 bg-gray-50 rounded-lg">
                                <p class="text-gray-500">No hay módulos de enfermería en este piso</p>
                                <a href="{{ route('modulos.create', ['piso_id' => $piso->id]) }}" class="mt-2 inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Agregar Primer Módulo
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
