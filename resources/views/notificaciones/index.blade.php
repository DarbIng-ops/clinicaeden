@extends('layouts.adminlte')

@section('title', 'Notificaciones')

@section('page-title', 'Mis Notificaciones')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Todas las Notificaciones</h3>
                <div class="card-tools">
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <form action="{{ route('notificaciones.marcar-todas-leidas') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-check"></i> Marcar todas como leídas
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover">
                    <tbody>
                        @forelse($notificaciones as $notificacion)
                            <tr class="{{ is_null($notificacion->read_at) ? 'bg-light' : '' }}">
                                <td style="width: 10px">
                                    @if(is_null($notificacion->read_at))
                                        <span class="badge badge-primary">Nuevo</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $notificacion->data['mensaje'] ?? 'Notificación' }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $notificacion->created_at->diffForHumans() }}
                                    </small>
                                </td>
                                <td style="width: 100px" class="text-right">
                                    @if(isset($notificacion->data['url']))
                                        <a href="{{ $notificacion->data['url'] }}" class="btn btn-sm btn-primary">
                                            Ver
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    <p>No tienes notificaciones</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($notificaciones->hasPages())
                <div class="card-footer">
                    {{ $notificaciones->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection