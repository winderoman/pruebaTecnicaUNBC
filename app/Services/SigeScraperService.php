<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;

class SigeScraperService
{
    private $baseUrl;
    private $username;
    private $password;
    private $rbd;
    private $cookies = [];

    public function __construct()
    {
        $this->baseUrl = config('services.sige.url', env('SIGE_URL'));
        $this->username = config('services.sige.username', env('SIGE_USERNAME'));
        $this->password = config('services.sige.password', env('SIGE_PASSWORD'));
        $this->rbd = config('services.sige.rbd', env('SIGE_RBD'));
    }

    /**
     * Autenticarse en la plataforma SIGE
     */
    public function login(): bool
    {
        try {
            Log::info('Intentando login en SIGE', ['username' => $this->username]);

            // Primera petición para obtener cookies y el formulario
            $response = Http::withOptions([
                'verify' => false, // Solo para desarrollo
                'allow_redirects' => true,
            ])->get($this->baseUrl);

            if (!$response->successful()) {
                Log::error('Error al cargar página de login', ['status' => $response->status()]);
                return false;
            }

            // Extraer cookies de sesión
            $cookies = $response->cookies()->toArray();
            $this->cookies = $cookies;

            // Realizar login
            $loginResponse = Http::withOptions([
                'verify' => false,
                'allow_redirects' => true,
            ])
            ->withCookies($cookies, parse_url($this->baseUrl, PHP_URL_HOST))
            ->asForm()
            ->post($this->baseUrl, [
                'usuario' => $this->username,
                'password' => $this->password,
                // Agregar otros campos del formulario según sea necesario
            ]);

            // Verificar si el login fue exitoso
            if ($loginResponse->successful()) {
                // Actualizar cookies después del login
                $this->cookies = array_merge($this->cookies, $loginResponse->cookies()->toArray());
                
                Log::info('Login exitoso en SIGE');
                return true;
            }

            Log::error('Login fallido', ['status' => $loginResponse->status()]);
            return false;

        } catch (\Exception $e) {
            Log::error('Excepción durante login', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Descargar archivo de Idoneidad Docente
     */
    public function descargarIdoneidadDocente(int $anio = null): ?string
    {
        try {
            if (empty($this->cookies)) {
                if (!$this->login()) {
                    Log::error('No se pudo autenticar para descargar archivo');
                    return null;
                }
            }

            $anio = $anio ?? date('Y');
            
            Log::info('Descargando Idoneidad Docente', ['año' => $anio]);

            // URL de exportación (ajustar según la plataforma real)
            $exportUrl = $this->baseUrl . '/DatosGenerales/IdoneidadDocente/Exportar';

            $response = Http::withOptions([
                'verify' => false,
                'allow_redirects' => true,
            ])
            ->withCookies($this->cookies, parse_url($this->baseUrl, PHP_URL_HOST))
            ->post($exportUrl, [
                'anio' => $anio,
                'rbd' => $this->rbd,
                'formato' => 'CSV', // o 'EXCEL' según disponibilidad
                'exportar_todo' => true,
            ]);

            if ($response->successful()) {
                // Guardar archivo temporalmente
                $filename = 'idoneidad_docente_' . $anio . '_' . date('YmdHis') . '.csv';
                $path = 'imports/' . $filename;
                
                Storage::put($path, $response->body());
                
                Log::info('Archivo descargado exitosamente', ['path' => $path]);
                return storage_path('app/' . $path);
            }

            Log::error('Error al descargar archivo', ['status' => $response->status()]);
            return null;

        } catch (\Exception $e) {
            Log::error('Excepción al descargar archivo', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Extraer datos del HTML (método alternativo si descarga directa no funciona)
     */
    public function extraerDatosDesdeTabla(): array
    {
        try {
            if (empty($this->cookies)) {
                if (!$this->login()) {
                    return [];
                }
            }

            // URL de la página con los datos
            $url = $this->baseUrl . '/DatosGenerales/IdoneidadDocente';

            $response = Http::withOptions([
                'verify' => false,
            ])
            ->withCookies($this->cookies, parse_url($this->baseUrl, PHP_URL_HOST))
            ->get($url);

            if (!$response->successful()) {
                return [];
            }

            $crawler = new Crawler($response->body());
            $datos = [];

            // Ejemplo de extracción (ajustar selectores según HTML real)
            $crawler->filter('table tbody tr')->each(function (Crawler $row) use (&$datos) {
                $cols = $row->filter('td');
                
                if ($cols->count() > 0) {
                    $datos[] = [
                        'rut' => trim($cols->eq(0)->text()),
                        'nombre_completo' => trim($cols->eq(1)->text()),
                        'asignatura' => trim($cols->eq(2)->text()),
                        'nivel' => trim($cols->eq(3)->text()),
                        'horas_contrato' => trim($cols->eq(4)->text()),
                        'titulo_profesional' => trim($cols->eq(5)->text()),
                        'idoneidad' => trim($cols->eq(6)->text()),
                    ];
                }
            });

            Log::info('Datos extraídos desde tabla HTML', ['total' => count($datos)]);
            return $datos;

        } catch (\Exception $e) {
            Log::error('Error al extraer datos desde HTML', ['error' => $e->getMessage()]);
            return [];
        }
    }
}