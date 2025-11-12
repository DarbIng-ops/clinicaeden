<div>
    <div class="row">
        <!-- Información del Paciente -->
        <div class="col-md-4">
            <!-- Perfil -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        @if($paciente->foto)
                            <img class="profile-user-img img-fluid img-circle" 
                                 src="{{ Storage::url($paciente->foto) }}" 
                                 alt="Foto del paciente">
                        @else
                            <img class="profile-user-img img-fluid img-circle" 
                                 src="https://ui-avatars.com/api/?name={{ urlencode($paciente->nombre_completo) }}&size=128&background=007bff&color=fff" 
                                 alt="Avatar">
                        @endif
                    </div>

                    <h3 class="profile-username text-center">{{ $paciente->nombre_completo }}</h3>

                    <p class="text-muted text-center">
                        <i class="fas fa-id-card"></i> {{ $paciente->dni }}
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Edad</b> <a class="float-right">{{ $paciente->edad }} años</a>
                        </li>
                        <li class="list-group-item">
                            <b>Sexo</b> <a class="float-right">{{ $paciente->sexo }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Tipo de Sangre</b> <a class="float-right">{{ $paciente->tipo_sangre ?? 'N/A' }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Estado</b> 
                            <a class="float-right">
                                <span class="badge badge-success">Activo</span>
                            </a>
                        </li>
                    </ul>

                    <a href="{{ route('recepcion.pacientes.editar', $paciente) }}" class="btn btn-warning btn-block">
                        <i class="fas fa-edit"></i> Editar Información
                    </a>
                    <a href="{{ route('recepcion.pacientes.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left"></i> Volver al Listado
                    </a>
                </div>
            </div>

            <!-- Información de Contacto -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-phone"></i> Contacto</h3>
                </div>
                <div class="card-body">
                    <strong><i class="fas fa-phone mr-1"></i> Teléfono</strong>
                    <p class="text-muted">{{ $paciente->telefono }}</p>
                    <hr>

                    <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
                    <p class="text-muted">{{ $paciente->email ?? 'No registrado' }}</p>
                    <hr>

                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Dirección</strong>
                    <p class="text-muted">{{ $paciente->direccion }}</p>
                    <p class="text-muted">{{ $paciente->ciudad }}</p>
                </div>
            </div>

            <!-- Contacto de Emergencia -->
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-phone-volume"></i> Emergencia</h3>
                </div>
                <div class="card-body">
                    <strong>Nombre</strong>
                    <p class="text-muted">{{ $paciente->contacto_emergencia_nombre }}</p>
                    <hr>
                    <strong>Teléfono</strong>
                    <p class="text-muted">{{ $paciente->contacto_emergencia_telefono }}</p>
                </div>
            </div>
        </div>

        <!-- Información Médica y Historial -->
        <div class="col-md-8">
            <!-- Información Médica -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-notes-medical"></i> Información Médica</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong><i class="fas fa-allergies mr-1"></i> Alergias</strong>
                            <p class="text-muted">
                                {{ $paciente->alergias ?? 'Ninguna registrada' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-procedures mr-1"></i> Enfermedades Crónicas</strong>
                            <p class="text-muted">
                                {{ $paciente->enfermedades_cronicas ?? 'Ninguna registrada' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs: Historial, Citas, Consultas -->
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="#historial" data-toggle="tab">
                                <i class="fas fa-history"></i> Historial Clínico
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#citas" data-toggle="tab">
                                <i class="fas fa-calendar-check"></i> Citas ({{ $paciente->citas->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#consultas" data-toggle="tab">
                                <i class="fas fa-file-medical"></i> Consultas ({{ $paciente->consultas->count() }})
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Historial Clínico -->
                        <div class="active tab-pane" id="historial">
                            @if($paciente->historiaClinica)
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Historia clínica creada el: 
                                    {{ $paciente->historiaClinica->created_at->format('d/m/Y') }}
                                </div>

                                @if($paciente->consultas->count() > 0)
                                    <div class="timeline">
                                        @foreach($paciente->consultas->sortByDesc('fecha_consulta') as $consulta)
                                            <div>
                                                <i class="fas fa-stethoscope bg-blue"></i>
                                                <div class="timeline-item">
                                                    <span class="time">
                                                        <i class="fas fa-clock"></i> 
                                                        {{ $consulta->fecha_consulta->format('d/m/Y H:i') }}
                                                    </span>
                                                    <h3 class="timeline-header">
                                                        Consulta con Dr. {{ $consulta->medico->name }}
                                                    </h3>
                                                    <div class="timeline-body">
                                                        <strong>Motivo:</strong> {{ $consulta->motivo }}<br>
                                                        @if($consulta->diagnostico)
                                                            <strong>Diagnóstico:</strong> {{ $consulta->diagnostico }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div>
                                            <i class="fas fa-clock bg-gray"></i>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        No hay consultas registradas aún.
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle"></i> 
                                    Historia clínica no creada.
                                </div>
                            @endif
                        </div>

                        <!-- Citas -->
                        <div class="tab-pane" id="citas">
                            @if($paciente->citas->count() > 0)
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Hora</th>
                                            <th>Médico</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($paciente->citas->sortByDesc('fecha') as $cita)
                                            <tr>
                                                <td>{{ $cita->fecha->format('d/m/Y') }}</td>
                                                <td>{{ $cita->hora->format('H:i') }}</td>
                                                <td>{{ $cita->medico->name }}</td>
                                                <td>
                                                    @if($cita->estado === 'pendiente')
                                                        <span class="badge badge-warning">Pendiente</span>
                                                    @elseif($cita->estado === 'completada')
                                                        <span class="badge badge-success">Completada</span>
                                                    @elseif($cita->estado === 'cancelada')
                                                        <span class="badge badge-danger">Cancelada</span>
                                                    @else
                                                        <span class="badge badge-info">{{ ucfirst($cita->estado) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No hay citas registradas.
                                </div>
                            @endif
                        </div>

                        <!-- Consultas -->
                        <div class="tab-pane" id="consultas">
                            @if($paciente->consultas->count() > 0)
                                @foreach($paciente->consultas->sortByDesc('fecha_consulta') as $consulta)
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">
                                                {{ $consulta->fecha_consulta->format('d/m/Y H:i') }} - 
                                                Dr. {{ $consulta->medico->name }}
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Motivo:</strong> {{ $consulta->motivo }}</p>
                                            @if($consulta->sintomas)
                                                <p><strong>Síntomas:</strong> {{ $consulta->sintomas }}</p>
                                            @endif
                                            @if($consulta->diagnostico)
                                                <p><strong>Diagnóstico:</strong> {{ $consulta->diagnostico }}</p>
                                            @endif
                                            @if($consulta->observaciones)
                                                <p><strong>Observaciones:</strong> {{ $consulta->observaciones }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No hay consultas registradas.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>