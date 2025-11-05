<?php

namespace App\Services;

use App\Models\Institucion;
use App\Models\DocenteIdoneidad;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CsvImportService
{
    private $stats = [
        'total' => 0,
        'importados' => 0,
        'actualizados' => 0,
        'errores' => 0,
        'errores_detalle' => [],
    ];

    /**
     * Importar archivo CSV de Idoneidad Docente
     */
    public function importarIdoneidadDocente(string $filePath, ?string $fechaCarga = null): array
    {
        try {
            if (!file_exists($filePath)) {
                throw new \Exception("Archivo no encontrado: {$filePath}");
            }

            $fechaCarga = $fechaCarga ?? Carbon::now()->format('Y-m-d');

            Log::info('Iniciando importación de Idoneidad Docente', [
                'archivo' => $filePath,
                'fecha_carga' => $fechaCarga
            ]);

            DB::beginTransaction();

            // Detectar encoding del archivo
            $content = file_get_contents($filePath);
            $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
            
            if ($encoding && $encoding !== 'UTF-8') {
                // Convertir a UTF-8
                $content = mb_convert_encoding($content, 'UTF-8', $encoding);
                file_put_contents($filePath, $content);
                Log::info("Archivo convertido de {$encoding} a UTF-8");
            }

            $handle = fopen($filePath, 'r');
            if ($handle === false) {
                throw new \Exception("No se pudo abrir el archivo");
            }

            // Leer y validar encabezados
            $headers = fgetcsv($handle, 0, ';');
            if (!$this->validarEncabezados($headers)) {
                throw new \Exception("El archivo no tiene el formato esperado");
            }

            $lineNumber = 1;
            while (($data = fgetcsv($handle, 0, ';')) !== false) {
                $lineNumber++;
                $this->stats['total']++;

                try {
                    $this->procesarFilaDocente($data, $fechaCarga);
                } catch (\Exception $e) {
                    $this->stats['errores']++;
                    $this->stats['errores_detalle'][] = [
                        'linea' => $lineNumber,
                        'error' => $e->getMessage(),
                        'datos' => implode(';', $data)
                    ];
                    Log::warning("Error en línea {$lineNumber}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            fclose($handle);
            DB::commit();

            Log::info('Importación completada', $this->stats);

            return $this->stats;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en importación', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Validar que los encabezados sean correctos
     */
    private function validarEncabezados(array $headers): bool
    {
        $esperados = [
            'DOC_RUN', 'DOC_DV', 'DOC_NOMBRE', 'DOC_PATERNO', 'DOC_MATERNO',
            'DOC_GENERO', 'DOC_FEC_NAC', 'RBD', 'NOMBRE_ESTA'
        ];

        foreach ($esperados as $esperado) {
            if (!in_array($esperado, $headers)) {
                Log::error("Falta encabezado: {$esperado}");
                return false;
            }
        }

        return true;
    }

    /**
     * Procesar una fila del CSV
     */
    private function procesarFilaDocente(array $data, string $fechaCarga): void
    {
        // Mapear datos del CSV
        $docRun = trim($data[0]);
        $docDv = trim($data[1]);
        $rbd = trim($data[7]);
        $nombreEsta = trim($data[8]);

        // Validaciones básicas
        if (empty($docRun) || empty($rbd)) {
            throw new \Exception("RUT o RBD vacío");
        }

        // Buscar o crear institución
        $institucion = Institucion::findOrCreateByRbd($rbd, $nombreEsta);

        // Preparar datos del docente con limpieza de encoding
        $docenteData = [
            'institucion_id' => $institucion->id,
            'doc_run' => $docRun,
            'doc_dv' => $docDv,
            'doc_nombre' => $this->limpiarTexto($data[2] ?? ''),
            'doc_paterno' => $this->limpiarTexto($data[3] ?? ''),
            'doc_materno' => $this->limpiarTexto($data[4] ?? ''),
            'doc_genero' => strtoupper(trim($data[5] ?? '')),
            'doc_fec_nac' => $this->parsearFecha($data[6] ?? ''),
            'rbd' => $rbd,
            'nombre_esta' => $this->limpiarTexto($nombreEsta),
            'funcion_principal' => $this->limpiarTexto($data[9] ?? ''),
            'funcion_secundaria' => $this->limpiarTexto($data[10] ?? '') ?: null,
            'horas_contrato' => (int)($data[11] ?? 0),
            'horas_ingles' => (int)($data[12] ?? 0),
            'horas_directivas' => (int)($data[13] ?? 0),
            'horas_tecnico_pedagogica' => (int)($data[14] ?? 0),
            'horas_aula' => (int)($data[15] ?? 0),
            'horas_pedagogicas_funcion_1' => (int)($data[16] ?? 0),
            'horas_pedagogicas_funcion_2' => (int)($data[17] ?? 0),
            'sector_funcion_1' => $this->limpiarTexto($data[18] ?? '') ?: null,
            'sub_sector_funcion_1' => $this->limpiarTexto($data[19] ?? '') ?: null,
            'sector_funcion_2' => $this->limpiarTexto($data[20] ?? '') ?: null,
            'sub_sector_funcion_2' => $this->limpiarTexto($data[21] ?? '') ?: null,
            'estado_idoneidad' => strtoupper(trim($data[22] ?? 'PENDIENTE')),
            'fecha_carga' => $fechaCarga,
        ];

        // Verificar si es nuevo o actualización
        $existe = DocenteIdoneidad::where([
            'institucion_id' => $institucion->id,
            'doc_run' => $docRun,
            'doc_dv' => $docDv,
            'fecha_carga' => $fechaCarga,
        ])->exists();

        // Crear o actualizar
        DocenteIdoneidad::createOrUpdate($docenteData);

        if ($existe) {
            $this->stats['actualizados']++;
        } else {
            $this->stats['importados']++;
        }
    }

    /**
 * Limpiar texto (eliminar caracteres especiales y errores de codificación)
 */
private function limpiarTexto(string $texto): string
{
    $texto = trim($texto);

    // Si está vacío o es el marcador "--", retornar vacío
    if ($texto === '' || $texto === '--') {
        return '';
    }

    // Asegurar que el texto esté en UTF-8
    if (!mb_check_encoding($texto, 'UTF-8')) {
        $texto = mb_convert_encoding($texto, 'UTF-8', 'ISO-8859-1');
    }

    // Normalizar caracteres mal codificados comunes (ISO → UTF-8)
    $reemplazos = [
        'Ã¡' => 'á', 'Ã ' => 'à', 'Ã¢' => 'â', 'Ã£' => 'ã', 'Ã¤' => 'ä',
        'Ã©' => 'é', 'Ã¨' => 'è', 'Ãª' => 'ê', 'Ã«' => 'ë',
        'Ã­' => 'í', 'Ã¬' => 'ì', 'Ã®' => 'î', 'Ã¯' => 'ï',
        'Ã³' => 'ó', 'Ã²' => 'ò', 'Ã´' => 'ô', 'Ãµ' => 'õ', 'Ã¶' => 'ö',
        'Ãº' => 'ú', 'Ã¹' => 'ù', 'Ã»' => 'û', 'Ã¼' => 'ü',
        'Ã±' => 'ñ', 'Ã‘' => 'Ñ',
        'Ã' => 'Á', 'Ã€' => 'À', 'Ã‚' => 'Â', 'Ãƒ' => 'Ã', 'Ã„' => 'Ä',
        'Ã‰' => 'É', 'Ãˆ' => 'È', 'ÃŠ' => 'Ê', 'Ã‹' => 'Ë',
        'ÃÍ' => 'Í', 'ÃŒ' => 'Ì', 'ÃŽ' => 'Î', 'ÃÏ' => 'Ï',
        'Ã“' => 'Ó', 'Ã’' => 'Ò', 'Ã”' => 'Ô', 'Ã•' => 'Õ', 'Ã–' => 'Ö',
        'Ãš' => 'Ú', 'Ã™' => 'Ù', 'Ã›' => 'Û', 'Ãœ' => 'Ü',
    ];

    $texto = strtr($texto, $reemplazos);

    // Remover cualquier caracter no imprimible o extraño
    $texto = preg_replace('/[^\PC\s]/u', '', $texto);

    // Normalizar espacios
    $texto = preg_replace('/\s+/', ' ', $texto);

    return trim($texto);
}


    /**
     * Parsear fecha en formato DD/MM/YY
     */
    private function parsearFecha(?string $fecha): ?string
    {
        if (empty($fecha) || $fecha === '--') {
            return null;
        }

        try {
            // Formato: 05/04/64
            $partes = explode('/', $fecha);
            if (count($partes) === 3) {
                $dia = str_pad($partes[0], 2, '0', STR_PAD_LEFT);
                $mes = str_pad($partes[1], 2, '0', STR_PAD_LEFT);
                $anio = $partes[2];

                // Convertir año de 2 dígitos a 4
                $anio = (int)$anio;
                $anio = $anio > 30 ? "19{$anio}" : "20{$anio}";

                return "{$anio}-{$mes}-{$dia}";
            }
        } catch (\Exception $e) {
            Log::warning("Error al parsear fecha: {$fecha}");
        }

        return null;
    }

    /**
     * Obtener estadísticas de la importación
     */
    public function getStats(): array
    {
        return $this->stats;
    }
}