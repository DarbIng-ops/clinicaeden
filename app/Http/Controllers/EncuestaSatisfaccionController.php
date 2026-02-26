<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EncuestaSatisfaccion;
use App\Models\Hospitalizacion;
use App\Models\Consulta;
use App\Models\Paciente;
use Illuminate\Support\Facades\Auth;

class EncuestaSatisfaccionController extends Controller
{
    public function index()
    {
        $encuestas = EncuestaSatisfaccion::with(['paciente', 'hospitalizacion', 'consulta'])
            ->orderBy('fecha_encuesta', 'desc')
            ->get();

        return view('encuestas.index', compact('encuestas'));
    }

    public function create(Request $request)
    {
        $paciente = null;
        $hospitalizacion = null;
        $consulta = null;

        if ($request->has('paciente_id')) {
            $paciente = Paciente::findOrFail($request->paciente_id);
            
            if ($request->has('hospitalizacion_id')) {
                $hospitalizacion = Hospitalizacion::findOrFail($request->hospitalizacion_id);
            }
            
            if ($request->has('consulta_id')) {
                $consulta = \App\Models\Consulta::findOrFail($request->consulta_id);
            }
        }

        return view('encuestas.create', compact('paciente', 'hospitalizacion', 'consulta'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'hospitalizacion_id' => 'nullable|exists:hospitalizaciones,id',
            'consulta_id' => 'nullable|exists:consultas,id',
            'atencion_medica' => 'nullable|integer|min:1|max:5',
            'atencion_enfermeria' => 'nullable|integer|min:1|max:5',
            'limpieza_habitacion' => 'nullable|integer|min:1|max:5',
            'comida' => 'nullable|integer|min:1|max:5',
            'personal_recepcion' => 'nullable|integer|min:1|max:5',
            'tiempo_espera' => 'nullable|integer|min:1|max:5',
            'calidad_general' => 'nullable|integer|min:1|max:5',
            'comentarios' => 'nullable|string|max:1000',
            'recomendaria' => 'nullable|boolean',
        ]);

        $encuesta = EncuestaSatisfaccion::create([
            'paciente_id' => $request->paciente_id,
            'hospitalizacion_id' => $request->hospitalizacion_id,
            'consulta_id' => $request->consulta_id,
            'recepcion_id' => Auth::id(),
            'atencion_medica' => $request->atencion_medica,
            'atencion_enfermeria' => $request->atencion_enfermeria,
            'limpieza_habitacion' => $request->limpieza_habitacion,
            'comida' => $request->comida,
            'personal_recepcion' => $request->personal_recepcion,
            'tiempo_espera' => $request->tiempo_espera,
            'calidad_general' => $request->calidad_general,
            'comentarios' => $request->comentarios,
            'recomendaria' => $request->recomendaria,
            'fecha_encuesta' => now(),
        ]);

        return redirect()->route('encuestas.show', $encuesta)->with('success', 'Encuesta de satisfacción registrada exitosamente.');
    }

    public function show(EncuestaSatisfaccion $encuesta)
    {
        $encuesta->load(['paciente', 'hospitalizacion', 'consulta', 'recepcion']);
        return view('encuestas.show', compact('encuesta'));
    }

    public function estadisticas()
    {
        $totalEncuestas = EncuestaSatisfaccion::count();
        $encuestasExcelentes = EncuestaSatisfaccion::excelentes()->count();
        $encuestasRecomendarian = EncuestaSatisfaccion::recomendarian()->count();
        
        $promedioGeneral = EncuestaSatisfaccion::selectRaw('AVG(calidad_general) as promedio')
            ->whereNotNull('calidad_general')
            ->value('promedio') ?? 0;

        $promedioAtencionMedica = EncuestaSatisfaccion::selectRaw('AVG(atencion_medica) as promedio')
            ->whereNotNull('atencion_medica')
            ->value('promedio') ?? 0;

        $promedioAtencionEnfermeria = EncuestaSatisfaccion::selectRaw('AVG(atencion_enfermeria) as promedio')
            ->whereNotNull('atencion_enfermeria')
            ->value('promedio') ?? 0;

        $promedioLimpieza = EncuestaSatisfaccion::selectRaw('AVG(limpieza_habitacion) as promedio')
            ->whereNotNull('limpieza_habitacion')
            ->value('promedio') ?? 0;

        $promedioComida = EncuestaSatisfaccion::selectRaw('AVG(comida) as promedio')
            ->whereNotNull('comida')
            ->value('promedio') ?? 0;

        $promedioRecepcion = EncuestaSatisfaccion::selectRaw('AVG(personal_recepcion) as promedio')
            ->whereNotNull('personal_recepcion')
            ->value('promedio') ?? 0;

        $promedioTiempoEspera = EncuestaSatisfaccion::selectRaw('AVG(tiempo_espera) as promedio')
            ->whereNotNull('tiempo_espera')
            ->value('promedio') ?? 0;

        $porcentajeExcelentes = $totalEncuestas > 0 ? ($encuestasExcelentes / $totalEncuestas) * 100 : 0;
        $porcentajeRecomendarian = $totalEncuestas > 0 ? ($encuestasRecomendarian / $totalEncuestas) * 100 : 0;

        return view('encuestas.estadisticas', compact(
            'totalEncuestas',
            'encuestasExcelentes',
            'encuestasRecomendarian',
            'promedioGeneral',
            'promedioAtencionMedica',
            'promedioAtencionEnfermeria',
            'promedioLimpieza',
            'promedioComida',
            'promedioRecepcion',
            'promedioTiempoEspera',
            'porcentajeExcelentes',
            'porcentajeRecomendarian'
        ));
    }

    public function buscarPaciente(Request $request)
    {
        $dni = $request->input('dni');
        $paciente = Paciente::where('dni', $dni)->where('activo', true)->first();

        if (!$paciente) {
            return response()->json(['error' => 'Paciente no encontrado'], 404);
        }

        // Buscar hospitalizaciones recientes (últimos 30 días)
        $hospitalizaciones = Hospitalizacion::where('paciente_id', $paciente->id)
            ->where('fecha_egreso', '>=', now()->subDays(30))
            ->where('estado', 'completado')
            ->with('habitacion')
            ->get();

        // Buscar consultas recientes (últimos 30 días)
        $consultas = \App\Models\Consulta::where('paciente_id', $paciente->id)
            ->where('fecha_consulta', '>=', now()->subDays(30))
            ->with('medico')
            ->get();

        return response()->json([
            'paciente' => $paciente,
            'hospitalizaciones' => $hospitalizaciones,
            'consultas' => $consultas
        ]);
    }
}