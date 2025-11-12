@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="bg-green-600 text-white px-6 py-4 rounded-t-lg">
            <h1 class="text-2xl font-bold">Procesar Salida del Paciente</h1>
        </div>

        <div class="p-6 space-y-6">
            <!-- Datos del Paciente -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h2 class="text-lg font-bold text-gray-900 mb-3">Datos del Paciente</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-600">Nombre:</span>
                        <p class="font-medium text-lg">{{ $paciente->nombres }} {{ $paciente->apellidos }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">DNI:</span>
                        <p class="font-medium text-lg">{{ $paciente->dni }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Teléfono:</span>
                        <p class="font-medium">{{ $paciente->telefono ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Email:</span>
                        <p class="font-medium">{{ $paciente->email ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Información de Pago -->
            <div class="bg-green-50 rounded-lg p-4 border-l-4 border-green-500">
                <h2 class="text-lg font-bold text-gray-900 mb-3">✓ Pago Confirmado</h2>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <span class="text-sm text-gray-600">Factura:</span>
                        <p class="font-medium">{{ $factura->numero_factura }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Monto Pagado:</span>
                        <p class="font-medium text-green-600">${{ number_format($factura->total, 2) }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Método:</span>
                        <p class="font-medium">{{ ucfirst($factura->metodo_pago) }}</p>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <form method="POST" action="{{ route('recepcion.confirmar-salida', $paciente->id) }}">
                @csrf
                
                <!-- Encuesta de Satisfacción -->
                <div class="bg-blue-50 rounded-lg p-6 border-l-4 border-blue-500 mb-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">⭐ Encuesta de Satisfacción</h2>
                    <p class="text-sm text-gray-600 mb-4">Por favor, solicite al paciente que califique su experiencia</p>
                    
                    <div class="space-y-6">
                        <!-- Calidad de Atención Médica -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Calidad de Atención Médica <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2" id="atencion-medica">
                                <button type="button" onclick="setRating('atencion_medica', 1)" data-value="1" class="rating-btn flex-1 border-2 border-gray-300 rounded-lg py-3 hover:bg-blue-50 transition">
                                    ⭐ Muy Mala
                                </button>
                                <button type="button" onclick="setRating('atencion_medica', 2)" data-value="2" class="rating-btn flex-1 border-2 border-gray-300 rounded-lg py-3 hover:bg-blue-50 transition">
                                    ⭐⭐ Mala
                                </button>
                                <button type="button" onclick="setRating('atencion_medica', 3)" data-value="3" class="rating-btn flex-1 border-2 border-gray-300 rounded-lg py-3 hover:bg-blue-50 transition">
                                    ⭐⭐⭐ Regular
                                </button>
                                <button type="button" onclick="setRating('atencion_medica', 4)" data-value="4" class="rating-btn flex-1 border-2 border-gray-300 rounded-lg py-3 hover:bg-blue-50 transition">
                                    ⭐⭐⭐⭐ Buena
                                </button>
                                <button type="button" onclick="setRating('atencion_medica', 5)" data-value="5" class="rating-btn flex-1 border-2 border-gray-300 rounded-lg py-3 hover:bg-blue-50 transition">
                                    ⭐⭐⭐⭐⭐ Excelente
                                </button>
                            </div>
                            <input type="hidden" name="atencion_medica" id="atencion_medica_value" required>
                        </div>

                        <!-- Tiempo de Espera -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tiempo de Espera <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2" id="tiempo-espera">
                                <button type="button" onclick="setRating('tiempo_espera', 1)" data-value="1" class="rating-btn flex-1 border-2 border-gray-300 rounded-lg py-3 hover:bg-blue-50 transition">
                                    ⭐ Muy Largo
                                </button>
                                <button type="button" onclick="setRating('tiempo_espera', 2)" data-value="2" class="rating-btn flex-1 border-2 border-gray-300 rounded-lg py-3 hover:bg-blue-50 transition">
                                    ⭐⭐ Largo
                                </button>
                                <button type="button" onclick="setRating('tiempo_espera', 3)" data-value="3" class="rating-btn flex-1 border-2 border-gray-300 rounded-lg py-3 hover:bg-blue-50 transition">
                                    ⭐⭐⭐ Aceptable
                                </button>
                                <button type="button" onclick="setRating('tiempo_espera', 4)" data-value="4" class="rating-btn flex-1 border-2 border-gray-300 rounded-lg py-3 hover:bg-blue-50 transition">
                                    ⭐⭐⭐⭐ Rápido
                                </button>
                                <button type="button" onclick="setRating('tiempo_espera', 5)" data-value="5" class="rating-btn flex-1 border-2 border-gray-300 rounded-lg py-3 hover:bg-blue-50 transition">
                                    ⭐⭐⭐⭐⭐ Muy Rápido
                                </button>
                            </div>
                            <input type="hidden" name="tiempo_espera" id="tiempo_espera_value" required>
                        </div>

                        <!-- Trato del Personal -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Trato del Personal <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2" id="trato-personal">
                                <button type="button" onclick="setRating('trato_personal', 1)" data-value="1" class="rating-btn flex-1 border-2 border-gray-300 rounded-lg py-3 hover:bg-blue-50 transition">
                                    ⭐
                                </button>
                                <button type="button" onclick="setRating('trato_personal', 2)" data-value="2" class="rating-btn flex-1 border-2 border-gray-300 rounded-lg py-3 hover:bg-blue-50 transition">
                                    ⭐⭐
                                </button>
                                <button type="button" onclick="setRating('trato_personal', 3)" data-value="3" class="rating-btn flex-1 border-2 border-gray-300 rounded-lg py-3 hover:bg-blue-50 transition">
                                    ⭐⭐⭐
                                </button>
                                <button type="button" onclick="setRating('trato_personal', 4)" data-value="4" class="rating-btn flex-1 border-2 border-gray-300 rounded-lg py-3 hover:bg-blue-50 transition">
                                    ⭐⭐⭐⭐
                                </button>
                                <button type="button" onclick="setRating('trato_personal', 5)" data-value="5" class="rating-btn flex-1 border-2 border-gray-300 rounded-lg py-3 hover:bg-blue-50 transition">
                                    ⭐⭐⭐⭐⭐
                                </button>
                            </div>
                            <input type="hidden" name="trato_personal" id="trato_personal_value" required>
                        </div>

                        <!-- Comentarios Adicionales -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Comentarios o Sugerencias (Opcional)
                            </label>
                            <textarea name="comentarios_encuesta" rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                                      placeholder="El paciente puede compartir comentarios adicionales..."></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Observaciones de Salida (Opcional)
                    </label>
                    <textarea name="observaciones_salida" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500"
                              placeholder="Ej: Se entregaron recetas, paciente en buen estado..."></textarea>
                </div>

                <div class="flex gap-4 pt-4 border-t">
                    <button type="submit" 
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition">
                        ✓ Confirmar Salida
                    </button>
                    <a href="{{ route('recepcion.salidas') }}" 
                       class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded-lg text-center transition">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setRating(category, value) {
    const containerId = category.replace(/_/g, '-');
    
    // Limpiar selección anterior
    const buttons = document.querySelectorAll(`#${containerId} .rating-btn`);
    buttons.forEach(btn => {
        btn.classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
        btn.classList.add('border-gray-300');
    });
    
    // Marcar nuevo rating
    const selectedBtn = document.querySelector(`#${containerId} [data-value="${value}"]`);
    if (selectedBtn) {
        selectedBtn.classList.remove('border-gray-300');
        selectedBtn.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
    }
    
    // Guardar valor
    const input = document.getElementById(`${category}_value`);
    if (input) {
        input.value = value;
    }
}

// Validar antes de enviar
document.querySelector('form').addEventListener('submit', function(e) {
    const required = ['atencion_medica', 'tiempo_espera', 'trato_personal'];
    let isValid = true;
    let missing = [];
    
    required.forEach(field => {
        const value = document.getElementById(`${field}_value`).value;
        if (!value || value === '') {
            isValid = false;
            missing.push(field.replace(/_/g, ' '));
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Por favor complete todas las calificaciones de la encuesta:\n- ' + missing.join('\n- '));
    }
});
</script>
@endsection

