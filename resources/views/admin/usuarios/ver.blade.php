@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto p-6">
<h1 class="text-2xl font-bold mb-6">Detalles del Usuario</h1>
<div class="bg-white shadow rounded p-6 space-y-4">
<div><strong>Nombre:</strong> {{ $usuario->name }} {{ $usuario->apellido }}</div>
<div><strong>DNI:</strong> {{ $usuario->dni }}</div>
<div><strong>Email:</strong> {{ $usuario->email }}</div>
<div><strong>Rol:</strong> {{ $usuario->role }}</div>
<div><strong>Teléfono:</strong> {{ $usuario->telefono ?? 'N/A' }}</div>
@if($usuario->profile_photo_path)
<div><strong>Foto:</strong><br><img src="{{ asset('storage/' . $usuario->profile_photo_path) }}" class="w-32 h-32 rounded"></div>
@endif
@if($usuario->diploma_path)
<div><strong>Diploma:</strong> <a href="{{ asset('storage/' . $usuario->diploma_path) }}" target="_blank" class="text-blue-600">Ver PDF</a></div>
@endif
</div>
<div class="mt-6 flex gap-4">
<a href="{{ route('admin.usuarios.editar', $usuario) }}" class="bg-blue-600 text-white px-4 py-2 rounded">Editar</a>
<a href="{{ route('admin.usuarios') }}" class="bg-gray-300 px-4 py-2 rounded">Volver</a>
</div>
</div>
@endsection
