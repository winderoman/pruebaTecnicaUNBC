<?php

namespace App\Http\Controllers;

use App\Models\DocenteIdoneidad;
use App\Models\Institucion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocenteIdoneidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DocenteIdoneidad::with('institucion');

        // Filtros
        if ($request->filled('rut')) {
            $rutBuscar = str_replace(['.', '-'], '', $request->rut);
            $query->where(DB::raw("CONCAT(doc_run, doc_dv)"), 'LIKE', "%{$rutBuscar}%");
        }

        if ($request->filled('nombre')) {
            $nombre = $request->nombre;
            $query->where(function($q) use ($nombre) {
                $q->where('doc_nombre', 'LIKE', "%{$nombre}%")
                  ->orWhere('doc_paterno', 'LIKE', "%{$nombre}%")
                  ->orWhere('doc_materno', 'LIKE', "%{$nombre}%");
            });
        }

        if ($request->filled('rbd')) {
            $query->where('rbd', $request->rbd);
        }

        if ($request->filled('estado_idoneidad')) {
            $query->where('estado_idoneidad', $request->estado_idoneidad);
        }

        if ($request->filled('funcion_principal')) {
            $query->where('funcion_principal', 'LIKE', "%{$request->funcion_principal}%");
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_carga', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_carga', '<=', $request->fecha_hasta);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        // Paginación
        $docentes = $query->paginate(20)->withQueryString();

        // Datos para filtros
        $instituciones = Institucion::orderBy('nombre')->get();
        $estadosIdoneidad = DocenteIdoneidad::select('estado_idoneidad')
            ->distinct()
            ->orderBy('estado_idoneidad')
            ->pluck('estado_idoneidad');

        return view('docentes.index', compact('docentes', 'instituciones', 'estadosIdoneidad'));
    }

    /**
     * Display the specified resource.
     */
    public function show(DocenteIdoneidad $docente)
    {
        $docente->load('institucion');
        return view('docentes.show', compact('docente'));
    }

    /**
     * Obtener estadísticas del dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_docentes' => DocenteIdoneidad::count(),
            'docentes_idoneos' => DocenteIdoneidad::where('estado_idoneidad', 'OK')->count(),
            'docentes_no_idoneos' => DocenteIdoneidad::where('estado_idoneidad', '!=', 'OK')->count(),
            'total_instituciones' => Institucion::count(),
        ];

        // Docentes por función principal
        $docentesPorFuncion = DocenteIdoneidad::select('funcion_principal', DB::raw('count(*) as total'))
            ->groupBy('funcion_principal')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Últimas cargas
        $ultimasCargas = DocenteIdoneidad::select('fecha_carga', 'rbd', 'nombre_esta', DB::raw('count(*) as total'))
            ->groupBy('fecha_carga', 'rbd', 'nombre_esta')
            ->orderBy('fecha_carga', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'docentesPorFuncion', 'ultimasCargas'));
    }
}