@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Bienvenido, {{ $user->name }}</h1>
        <p class="text-gray-600">Tu cuenta está siendo configurada. Por favor, contacta al administrador.</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Rol no asignado</h3>
            <p class="text-gray-600 mb-4">
                Tu cuenta no tiene un rol asignado. Por favor, contacta al administrador del sistema para que te asigne los permisos correspondientes.
            </p>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-700">
                    <strong>Información de tu cuenta:</strong><br>
                    Nombre: {{ $user->name }}<br>
                    Email: {{ $user->email }}<br>
                    Rol actual: {{ $user->role ?? 'No asignado' }}
                </p>
            </div>
        </div>
    </div>

    <div class="mt-6 text-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                Cerrar Sesión
            </button>
        </form>
    </div>
</div>
@endsection
