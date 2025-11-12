<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Estructura Hospitalaria - Pisos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Lista de Pisos</h3>
                        <a href="{{ route('pisos.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Crear Nuevo Piso
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($pisos as $piso)
                            <div class="border rounded-lg p-6 hover:shadow-lg transition-shadow">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h4 class="text-xl font-semibold text-gray-800">
                                            Piso {{ $piso->numero }} - {{ $piso->nombre }}
                                        </h4>
                                        @if($piso->descripcion)
                                            <p class="text-gray-600 mt-1">{{ $piso->descripcion }}</p>
                                        @endif
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('pisos.show', $piso) }}" class="text-blue-600 hover:text-blue-800">
                                            Ver Detalles
                                        </a>
                                        <a href="{{ route('pisos.edit', $piso) }}" class="text-green-600 hover:text-green-800">
                                            Editar
                                        </a>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div class="bg-gray-50 p-3 rounded">
                                        <div class="font-medium text-gray-700">Consultorios</div>
                                        <div class="text-2xl font-bold text-blue-600">{{ $piso->total_consultorios }}</div>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded">
                                        <div class="font-medium text-gray-700">Módulos</div>
                                        <div class="text-2xl font-bold text-green-600">{{ $piso->total_modulos }}</div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ route('pisos.show', $piso) }}" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded text-center block">
                                        Ver Estructura Completa
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($pisos->isEmpty())
                        <div class="text-center py-12">
                            <div class="text-gray-500 text-lg">No hay pisos registrados</div>
                            <a href="{{ route('pisos.create') }}" class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Crear Primer Piso
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
