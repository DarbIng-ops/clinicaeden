@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto p-6">
<h1 class="text-2xl font-bold mb-6">Crear Nuevo Usuario</h1>
<form method="POST" action="{{ route('admin.usuarios.store') }}" enctype="multipart/form-data" class="space-y-4">
@csrf
<div class="grid grid-cols-2 gap-4">
<div><label class="block text-sm font-medium mb-1">Nombre</label><input name="name" class="w-full border rounded px-3 py-2" required></div>
<div><label class="block text-sm font-medium mb-1">Apellido</label><input name="apellido" class="w-full border rounded px-3 py-2" required></div>
<div><label class="block text-sm font-medium mb-1">DNI</label><input name="dni" class="w-full border rounded px-3 py-2" required></div>
<div>
    <label class="block text-sm font-medium mb-1">Email institucional</label>
    <div class="flex rounded-lg border border-gray-300 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500">
        <input type="text"
               name="email_prefix"
               id="email_prefix"
               placeholder="juan.perez"
               class="flex-1 px-3 py-2 outline-none border-none"
               required
               oninput="document.getElementById('email_hidden').value = this.value + '@@clinicaeden.com'">
        <span class="bg-gray-100 px-3 py-2 text-gray-500 border-l border-gray-300 select-none whitespace-nowrap">
            @@clinicaeden.com
        </span>
    </div>
    <input type="hidden" name="email" id="email_hidden">
</div>
<div><label class="block text-sm font-medium mb-1">Password</label><input name="password" type="password" class="w-full border rounded px-3 py-2" required></div>
<div><label class="block text-sm font-medium mb-1">Rol</label><select name="role" class="w-full border rounded px-3 py-2" required><option value="admin">Admin</option><option value="recepcionista">Recepcionista</option><option value="medico_general">Médico General</option><option value="medico_especialista">Médico Especialista</option><option value="jefe_enfermeria">Jefe Enfermería</option><option value="auxiliar_enfermeria">Auxiliar Enfermería</option><option value="caja">Caja</option></select></div>
<div><label class="block text-sm font-medium mb-1">Teléfono</label><input name="telefono" class="w-full border rounded px-3 py-2"></div>
<div>
    <label class="block text-sm font-medium mb-1">Correo Personal</label>
    <input type="email" name="email_personal" placeholder="ejemplo@gmail.com" class="w-full border rounded px-3 py-2">
    <p class="text-xs text-gray-400 mt-1">Opcional. No se usa para el login.</p>
</div>
<div><label class="block text-sm font-medium mb-1">Fecha Nacimiento</label><input name="fecha_nacimiento" type="date" class="w-full border rounded px-3 py-2"></div>
<div><label class="block text-sm font-medium mb-1">Sexo</label><select name="sexo" class="w-full border rounded px-3 py-2"><option value="M">Masculino</option><option value="F">Femenino</option></select></div>
</div>
<div><label class="block text-sm font-medium mb-1">Dirección</label><textarea name="direccion" class="w-full border rounded px-3 py-2"></textarea></div>
<div><label class="block text-sm font-medium mb-1">Foto</label><input name="foto" type="file" accept="image/*" class="w-full border rounded px-3 py-2"></div>
<div><label class="block text-sm font-medium mb-1">Diploma (PDF)</label><input name="diploma" type="file" accept=".pdf" class="w-full border rounded px-3 py-2"></div>
<div class="flex gap-4"><button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Guardar</button><a href="{{ route('admin.usuarios') }}" class="bg-gray-300 px-6 py-2 rounded">Cancelar</a></div>
</form>
</div>
@endsection