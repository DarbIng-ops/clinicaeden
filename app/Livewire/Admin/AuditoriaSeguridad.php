<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AuditoriaAcceso;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * AuditoriaSeguridad
 *
 * Panel de auditoría de accesos y acciones críticas.
 * Implementación conforme a ISO 27001 — Control A.12.4.
 * Acceso exclusivo para rol admin.
 *
 * @package ClinicaEden
 * @author  Alirio Portilla
 * @version 3.0.0
 */
class AuditoriaSeguridad extends Component
{
    use WithPagination;

    /** @var string Filtro por tipo de acción */
    public string $filtro_accion = '';

    /** @var string Filtro por nombre de usuario */
    public string $filtro_usuario = '';

    /** @var string Fecha inicio del filtro */
    public string $fecha_desde = '';

    /** @var string Fecha fin del filtro */
    public string $fecha_hasta = '';

    protected $queryString = [
        'filtro_accion'  => ['except' => ''],
        'filtro_usuario' => ['except' => ''],
        'fecha_desde'    => ['except' => ''],
        'fecha_hasta'    => ['except' => ''],
    ];

    // ── Computed ──────────────────────────────────────────

    public function getRegistrosProperty(): LengthAwarePaginator
    {
        return AuditoriaAcceso::with('user')
            ->when($this->filtro_accion, fn ($q) =>
                $q->where('accion', $this->filtro_accion)
            )
            ->when($this->filtro_usuario, fn ($q) =>
                $q->whereHas('user', fn ($u) =>
                    $u->where('name', 'like', '%' . $this->filtro_usuario . '%')
                )
            )
            ->when($this->fecha_desde, fn ($q) =>
                $q->whereDate('created_at', '>=', $this->fecha_desde)
            )
            ->when($this->fecha_hasta, fn ($q) =>
                $q->whereDate('created_at', '<=', $this->fecha_hasta)
            )
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }

    // ── Acciones ──────────────────────────────────────────

    public function limpiarFiltros(): void
    {
        $this->reset(['filtro_accion', 'filtro_usuario', 'fecha_desde', 'fecha_hasta']);
        $this->resetPage();
    }

    public function updatingFiltroAccion(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroUsuario(): void
    {
        $this->resetPage();
    }

    // ── Render ────────────────────────────────────────────

    public function render()
    {
        return view('livewire.admin.auditoria-seguridad')
            ->extends('layouts.adminlte')
            ->section('content');
    }
}
