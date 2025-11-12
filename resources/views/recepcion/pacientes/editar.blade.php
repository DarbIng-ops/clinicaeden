@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Editar Paciente</h1>
        <div class="space-x-2">
            <a href="{{ route('recepcion.pacientes.show', $paciente) }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                <i class="fas fa-eye mr-2"></i>Ver
            </a>
            <a href="{{ route('recepcion.pacientes.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('recepcion.pacientes.update', $paciente) }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6">
            @csrf
            @method('PUT')
            
            <!-- Información Personal -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-user mr-2"></i>Información Personal
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="dni" class="block text-sm font-medium text-gray-700 mb-2">DNI *</label>
                        <input type="text" id="dni" name="dni" value="{{ old('dni', $paciente->dni) }}" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('dni') border-red-500 @enderror" 
                               placeholder="12345678" maxlength="8">
                        @error('dni')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="nombres" class="block text-sm font-medium text-gray-700 mb-2">Nombres *</label>
                        <input type="text" id="nombres" name="nombres" value="{{ old('nombres', $paciente->nombres) }}" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nombres') border-red-500 @enderror" 
                               placeholder="Juan Carlos">
                        @error('nombres')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="apellidos" class="block text-sm font-medium text-gray-700 mb-2">Apellidos *</label>
                        <input type="text" id="apellidos" name="apellidos" value="{{ old('apellidos', $paciente->apellidos) }}" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('apellidos') border-red-500 @enderror" 
                               placeholder="Pérez García">
                        @error('apellidos')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Nacimiento *</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento->format('Y-m-d')) }}" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fecha_nacimiento') border-red-500 @enderror">
                        @error('fecha_nacimiento')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="sexo" class="block text-sm font-medium text-gray-700 mb-2">Sexo *</label>
                        <select id="sexo" name="sexo" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('sexo') border-red-500 @enderror">
                            <option value="">Seleccionar</option>
                            <option value="M" {{ old('sexo', $paciente->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('sexo', $paciente->sexo) == 'F' ? 'selected' : '' }}>Femenino</option>
                            <option value="Otro" {{ old('sexo', $paciente->sexo) == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('sexo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">Foto</label>
                        @if($paciente->foto)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $paciente->foto) }}" alt="{{ $paciente->nombre_completo }}" class="h-20 w-20 rounded-full object-cover">
                                <p class="text-sm text-gray-500 mt-1">Foto actual</p>
                            </div>
                        @endif
                        <input type="file" id="foto" name="foto" accept="image/*" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('foto') border-red-500 @enderror">
                        @error('foto')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Formatos: JPEG, PNG, JPG, GIF. Máximo 2MB.</p>
                    </div>
                </div>
            </div>

            <!-- Información de Contacto -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-phone mr-2"></i>Información de Contacto
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">Teléfono *</label>
                        <input type="tel" id="telefono" name="telefono" value="{{ old('telefono', $paciente->telefono) }}" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('telefono') border-red-500 @enderror" 
                               placeholder="987654321">
                        @error('telefono')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $paciente->email) }}" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror" 
                               placeholder="juan@email.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">Dirección *</label>
                        <textarea id="direccion" name="direccion" rows="3" required 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('direccion') border-red-500 @enderror" 
                                  placeholder="Av. Principal 123, Lima, Perú">{{ old('direccion', $paciente->direccion) }}</textarea>
                        @error('direccion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="ciudad" class="block text-sm font-medium text-gray-700 mb-2">Ciudad *</label>
                        <input type="text" id="ciudad" name="ciudad" value="{{ old('ciudad', $paciente->ciudad) }}" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('ciudad') border-red-500 @enderror" 
                               placeholder="Montevideo">
                        @error('ciudad')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Información Médica -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-heartbeat mr-2"></i>Información Médica
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tipo_sangre" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Sangre</label>
                        <select id="tipo_sangre" name="tipo_sangre" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tipo_sangre') border-red-500 @enderror">
                            <option value="">Seleccionar</option>
                            <option value="A+" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="AB+" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'AB-' ? 'selected' : '' }}>AB-</option>
                            <option value="O+" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'O-' ? 'selected' : '' }}>O-</option>
                        </select>
                        @error('tipo_sangre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="alergias" class="block text-sm font-medium text-gray-700 mb-2">Alergias</label>
                        <input type="text" id="alergias" name="alergias" value="{{ old('alergias', $paciente->alergias) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('alergias') border-red-500 @enderror" 
                               placeholder="Penicilina, Mariscos">
                        @error('alergias')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="enfermedades_cronicas" class="block text-sm font-medium text-gray-700 mb-2">Enfermedades Crónicas</label>
                        <textarea id="enfermedades_cronicas" name="enfermedades_cronicas" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('enfermedades_cronicas') border-red-500 @enderror" 
                                  placeholder="Diabetes, Hipertensión, etc.">{{ old('enfermedades_cronicas', $paciente->enfermedades_cronicas) }}</textarea>
                        @error('enfermedades_cronicas')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contacto de Emergencia -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Contacto de Emergencia
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="contacto_emergencia_nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Contacto *</label>
                        <input type="text" id="contacto_emergencia_nombre" name="contacto_emergencia_nombre" value="{{ old('contacto_emergencia_nombre', $paciente->contacto_emergencia_nombre) }}" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contacto_emergencia_nombre') border-red-500 @enderror" 
                               placeholder="María García">
                        @error('contacto_emergencia_nombre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="contacto_emergencia_telefono" class="block text-sm font-medium text-gray-700 mb-2">Teléfono del Contacto *</label>
                        <input type="tel" id="contacto_emergencia_telefono" name="contacto_emergencia_telefono" value="{{ old('contacto_emergencia_telefono', $paciente->contacto_emergencia_telefono) }}" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contacto_emergencia_telefono') border-red-500 @enderror" 
                               placeholder="987654321">
                        @error('contacto_emergencia_telefono')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('recepcion.pacientes.show', $paciente) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>Actualizar Paciente
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Validación en tiempo real del DNI
document.getElementById('dni').addEventListener('input', function(e) {
    // Solo permitir números
    e.target.value = e.target.value.replace(/[^0-9]/g, '');
    
    // Limitar a 8 dígitos
    if (e.target.value.length > 8) {
        e.target.value = e.target.value.slice(0, 8);
    }
});

// Validación en tiempo real del teléfono
document.getElementById('telefono').addEventListener('input', function(e) {
    // Solo permitir números, +, -, espacios y paréntesis
    e.target.value = e.target.value.replace(/[^0-9+\-\s()]/g, '');
});

// Validación en tiempo real del teléfono de emergencia
document.getElementById('contacto_emergencia_telefono').addEventListener('input', function(e) {
    // Solo permitir números, +, -, espacios y paréntesis
    e.target.value = e.target.value.replace(/[^0-9+\-\s()]/g, '');
});

// Validación de fecha de nacimiento
document.getElementById('fecha_nacimiento').addEventListener('change', function(e) {
    const fechaNacimiento = new Date(e.target.value);
    const hoy = new Date();
    
    if (fechaNacimiento >= hoy) {
        alert('La fecha de nacimiento debe ser anterior a hoy.');
        e.target.value = '';
    }
});
</script>
@endsection