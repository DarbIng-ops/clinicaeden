@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto p-6">
<h1 class="text-2xl font-bold mb-6">Editar Usuario</h1>
<form method="POST" action="{{ route('admin.usuarios.actualizar', $usuario) }}" enctype="multipart/form-data" class="space-y-4">
@csrf
@method('PUT')
<div class="grid grid-cols-2 gap-4">
<div><label class="block text-sm font-medium mb-1">Nombre</label><input name="name" value="{{ $usuario->name }}" class="w-full border rounded px-3 py-2" required></div>
<div><label class="block text-sm font-medium mb-1">Apellido</label><input name="apellido" value="{{ $usuario->apellido }}" class="w-full border rounded px-3 py-2" required></div>
<div><label class="block text-sm font-medium mb-1">DNI</label><input name="dni" value="{{ $usuario->dni }}" class="w-full border rounded px-3 py-2" required></div>
<div><label class="block text-sm font-medium mb-1">Email</label><input name="email" type="email" value="{{ $usuario->email }}" class="w-full border rounded px-3 py-2" required></div>
<div><label class="block text-sm font-medium mb-1">Password</label><input name="password" type="password" class="w-full border rounded px-3 py-2" placeholder="Dejar vacío para mantener actual"></div>
<div><label class="block text-sm font-medium mb-1">Rol</label><select name="role" class="w-full border rounded px-3 py-2" required><option value="admin" {{ $usuario->role == 'admin' ? 'selected' : '' }}>Admin</option><option value="recepcionista" {{ $usuario->role == 'recepcionista' ? 'selected' : '' }}>Recepcionista</option><option value="medico_general" {{ $usuario->role == 'medico_general' ? 'selected' : '' }}>Médico General</option><option value="medico_especialista" {{ $usuario->role == 'medico_especialista' ? 'selected' : '' }}>Médico Especialista</option><option value="jefe_enfermeria" {{ $usuario->role == 'jefe_enfermeria' ? 'selected' : '' }}>Jefe Enfermería</option><option value="auxiliar_enfermeria" {{ $usuario->role == 'auxiliar_enfermeria' ? 'selected' : '' }}>Auxiliar Enfermería</option><option value="caja" {{ $usuario->role == 'caja' ? 'selected' : '' }}>Caja</option></select></div>
<div><label class="block text-sm font-medium mb-1">Teléfono</label><input name="telefono" value="{{ $usuario->telefono }}" class="w-full border rounded px-3 py-2"></div>
<div><label class="block text-sm font-medium mb-1">Fecha Nacimiento</label><input name="fecha_nacimiento" type="date" value="{{ $usuario->fecha_nacimiento }}" class="w-full border rounded px-3 py-2"></div>
<div><label class="block text-sm font-medium mb-1">Sexo</label><select name="sexo" class="w-full border rounded px-3 py-2"><option value="M" {{ $usuario->sexo == 'M' ? 'selected' : '' }}>Masculino</option><option value="F" {{ $usuario->sexo == 'F' ? 'selected' : '' }}>Femenino</option></select></div>
</div>
<div><label class="block text-sm font-medium mb-1">Dirección</label><textarea name="direccion" class="w-full border rounded px-3 py-2">{{ $usuario->direccion }}</textarea></div>
<div><label class="block text-sm font-medium mb-1">Foto</label><input name="foto" type="file" accept="image/*" class="w-full border rounded px-3 py-2"></div>
<div><label class="block text-sm font-medium mb-1">Diploma (PDF)</label><input name="diploma" type="file" accept=".pdf" class="w-full border rounded px-3 py-2"></div>
<div class="flex gap-4"><button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Actualizar</button><a href="{{ route('admin.usuarios') }}" class="bg-gray-300 px-6 py-2 rounded">Cancelar</a></div>
</form>
</div>
@endsection
