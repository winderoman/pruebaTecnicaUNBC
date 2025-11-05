<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CsvImportService;
use Illuminate\Support\Facades\Storage;

class ImportacionController extends Controller
{
    /**
     * Mostrar formulario de importación
     */
    public function index()
    {
        // Obtener historial de importaciones (últimas 10)
        $historial = \App\Models\DocenteIdoneidad::select('fecha_carga', 'rbd', 'nombre_esta', \DB::raw('count(*) as total'))
            ->groupBy('fecha_carga', 'rbd', 'nombre_esta')
            ->orderBy('fecha_carga', 'desc')
            ->limit(10)
            ->get();

        return view('importacion.index', compact('historial'));
    }

    /**
     * Procesar archivo subido
     */
    public function importar(Request $request, CsvImportService $importService)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
            'fecha_carga' => 'nullable|date',
        ], [
            'archivo.required' => 'Debe seleccionar un archivo CSV',
            'archivo.mimes' => 'El archivo debe ser formato CSV',
            'archivo.max' => 'El archivo no debe superar 10MB',
        ]);

        try {
            // Guardar archivo temporalmente
            $archivo = $request->file('archivo');
            $nombreArchivo = 'import_' . time() . '_' . $archivo->getClientOriginalName();
            $path = $archivo->storeAs('imports/temp', $nombreArchivo);
            $rutaCompleta = storage_path('app/' . $path);

            // Procesar importación
            $fechaCarga = $request->fecha_carga ?? now()->format('Y-m-d');
            $stats = $importService->importarIdoneidadDocente($rutaCompleta, $fechaCarga);

            // Mover archivo a carpeta de procesados
            Storage::move($path, 'imports/procesados/' . $nombreArchivo);

            // Preparar mensaje de éxito
            $mensaje = sprintf(
                'Importación exitosa: %d registros procesados, %d nuevos, %d actualizados',
                $stats['total'],
                $stats['importados'],
                $stats['actualizados']
            );

            if ($stats['errores'] > 0) {
                $mensaje .= sprintf(', %d errores', $stats['errores']);
            }

            return redirect()->route('importacion.index')
                ->with('success', $mensaje)
                ->with('stats', $stats);

        } catch (\Exception $e) {
            // Eliminar archivo temporal si existe
            if (isset($path)) {
                Storage::delete($path);
            }

            return redirect()->route('importacion.index')
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Descargar plantilla CSV de ejemplo
     */
    public function descargarPlantilla()
    {
        $headers = [
            'DOC_RUN', 'DOC_DV', 'DOC_NOMBRE', 'DOC_PATERNO', 'DOC_MATERNO',
            'DOC_GENERO', 'DOC_FEC_NAC', 'RBD', 'NOMBRE_ESTA', 'FUNCION_PRINCIPAL',
            'FUNCION_SECUNDARIA', 'HORAS_CONTRATO', 'HORAS_INGLES', 'HORAS_DIRECTIVAS',
            'HORAS_TECNICO_PEDAGOGICA', 'HORAS_AULA', 'HORAS_PEDAGOGICAS_FUNCION_1',
            'HORAS_PEDAGOGICAS_FUNCION_2', 'SECTOR_FUNCION_1', 'SUB_SECTOR_FUNCION_1',
            'SECTOR_FUNCION_2', 'SUB_SECTOR_FUNCION_2', 'ESTADO_IDONEIDAD'
        ];

        $ejemplo = [
            '12345678', '9', 'JUAN', 'PEREZ', 'GONZALEZ', 'M', '01/01/1980',
            '8001', 'LICEO EJEMPLO', 'DOCENTE DE AULA', '--', '40', '0', '0',
            '0', '35', '35', '0', 'MATEMATICAS', 'MATEMATICAS', '--', '--', 'OK'
        ];

        $content = implode(';', $headers) . "\n";
        $content .= implode(';', $ejemplo) . "\n";

        return response($content)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="plantilla_idoneidad_docente.csv"');
    }
}