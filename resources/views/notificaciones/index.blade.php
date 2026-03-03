@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mis Notificaciones</h1>
            <p class="text-gray-600">Centro de notificaciones del sistema</p>
        </div>
        @if(auth()->user()->notificacionesNoLeidas()->count() > 0)
            <form action="{{ route('notificaciones.marcar-todas-leidas') }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    <i class="fas fa-check-double mr-2"></i> Marcar todas como leídas
                </button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <tbody class="divide-y divide-gray-200">
                @forelse($notificaciones as $notificacion)
                    <tr class="{{ !$notificacion->leida ? 'bg-blue-50' : 'bg-white' }} hover:bg-gray-50">
                        <td class="px-4 py-4 w-20 text-center">
                            @if(!$notificacion->leida)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800">
                                    Nuevo
                                </span>
                            @else
                                <i class="fas fa-envelope-open text-gray-300"></i>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <p class="font-semibold text-gray-900">{{ $notificacion->titulo }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $notificacion->mensaje }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $notificacion->created_at->diffForHumans() }}
                            </p>
                        </td>
                        <td class="px-4 py-4 w-32 text-right">
                            @if(!$notificacion->leida)
                                <form action="{{ route('notificaciones.marcar-leida', $notificacion->id) }}"
                                      method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                        Marcar leída
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-16 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 block text-gray-300"></i>
                            <p class="text-lg font-medium">Sin notificaciones</p>
                            <p class="text-sm">No tienes notificaciones en este momento</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($notificaciones->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $notificaciones->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
