<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listado de Pacientes</h3>
            <div class="card-tools">
                <a href="{{ route('recepcion.pacientes.crear') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Paciente
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" 
                           wire:model.live="search" 
                           class="form-control" 
                           placeholder="Buscar por nombre, apellido o DNI...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>DNI</th>
                            <th>Nombre Completo</th>
                            <th>Edad</th>
                            <th>Teléfono</th>
                            <th>Ciudad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pacientes as $paciente)
                            <tr>
                                <td>{{ $paciente->dni }}</td>
                                <td>{{ $paciente->nombre_completo }}</td>
                                <td>{{ $paciente->edad }} años</td>
                                <td>{{ $paciente->telefono }}</td>
                                <td>{{ $paciente->ciudad }}</td>
                                <td>
                                    <a href="{{ route('recepcion.pacientes.ver', $paciente) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('recepcion.pacientes.editar', $paciente) }}" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    No se encontraron pacientes
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $pacientes->links() }}
        </div>
    </div>
</div>