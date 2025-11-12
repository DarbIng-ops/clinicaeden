<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Registrar Nuevo Paciente</h3>
        </div>
        <form wire:submit.prevent="guardar">
            <div class="card-body">
                
                @if (session()->has('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <h5><i class="fas fa-user"></i> Información Personal</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>DNI <span class="text-danger">*</span></label>
                            <input type="text" wire:model="dni" class="form-control" placeholder="12345678">
                            @error('dni') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nombres <span class="text-danger">*</span></label>
                            <input type="text" wire:model="nombres" class="form-control">
                            @error('nombres') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Apellidos <span class="text-danger">*</span></label>
                            <input type="text" wire:model="apellidos" class="form-control">
                            @error('apellidos') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fecha Nacimiento <span class="text-danger">*</span></label>
                            <input type="date" wire:model="fecha_nacimiento" class="form-control">
                            @error('fecha_nacimiento') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Sexo <span class="text-danger">*</span></label>
                            <select wire:model="sexo" class="form-control">
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Teléfono <span class="text-danger">*</span></label>
                            <input type="text" wire:model="telefono" class="form-control">
                            @error('telefono') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" wire:model="email" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Dirección <span class="text-danger">*</span></label>
                            <input type="text" wire:model="direccion" class="form-control">
                            @error('direccion') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Ciudad <span class="text-danger">*</span></label>
                            <input type="text" wire:model="ciudad" class="form-control">
                        </div>
                    </div>
                </div>

                <hr>
                <h5><i class="fas fa-heartbeat"></i> Información Médica</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tipo de Sangre</label>
                            <select wire:model="tipo_sangre" class="form-control">
                                <option value="">Seleccionar</option>
                                <option>A+</option>
                                <option>A-</option>
                                <option>B+</option>
                                <option>B-</option>
                                <option>AB+</option>
                                <option>AB-</option>
                                <option>O+</option>
                                <option>O-</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Alergias</label>
                            <textarea wire:model="alergias" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Enfermedades Crónicas</label>
                            <textarea wire:model="enfermedades_cronicas" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <hr>
                <h5><i class="fas fa-phone"></i> Contacto de Emergencia</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nombre <span class="text-danger">*</span></label>
                            <input type="text" wire:model="contacto_emergencia_nombre" class="form-control">
                            @error('contacto_emergencia_nombre') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Teléfono <span class="text-danger">*</span></label>
                            <input type="text" wire:model="contacto_emergencia_telefono" class="form-control">
                            @error('contacto_emergencia_telefono') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <a href="{{ route('recepcion.pacientes.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>