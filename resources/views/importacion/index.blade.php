<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Importar Datos - Idoneidad Docente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Mensajes de éxito/error -->
            @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">¡Éxito!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
                
                @if(session('stats'))
                <div class="mt-3 text-sm">
                    <strong>Detalles:</strong>
                    <ul class="list-disc list-inside mt-2">
                        <li>Total procesados: {{ session('stats')['total'] }}</li>
                        <li>Nuevos registros: {{ session('stats')['importados'] }}</li>
                        <li>Actualizados: {{ session('stats')['actualizados'] }}</li>
                        <li>Errores: {{ session('stats')['errores'] }}</li>
                    </ul>
                    
                    @if(session('stats')['errores'] > 0 && count(session('stats')['errores_detalle']) > 0)
                    <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded p-3">
                        <strong class="text-yellow-800">Errores encontrados:</strong>
                        <ul class="list-disc list-inside mt-2 text-yellow-700">
                            @foreach(array_slice(session('stats')['errores_detalle'], 0, 5) as $error)
                            <li>Línea {{ $error['linea'] }}: {{ $error['error'] }}</li>
                            @endforeach
                            @if(count(session('stats')['errores_detalle']) > 5)
                            <li class="text-gray-600">... y {{ count(session('stats')['errores_detalle']) - 5 }} errores más</li>
                            @endif
                        </ul>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error:</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Errores de validación:</strong>
                <ul class="list-disc list-inside mt-2">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Formulario de Importación -->
                <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Cargar Archivo CSV</h3>

                        <!-- Instrucciones -->
                        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-semibold text-blue-900 mb-2">Instrucciones:</h4>
                            <ol class="list-decimal list-inside text-sm text-blue-800 space-y-1">
                                <li>Descarga el archivo CSV desde la plataforma SIGE (Idoneidad Docente)</li>
                                <li>Opcionalmente, descarga la plantilla de ejemplo para ver el formato</li>
                                <li>Selecciona el archivo CSV a importar</li>
                                <li>El sistema detectará automáticamente duplicados</li>
                                <li>Los registros existentes serán actualizados automáticamente</li>
                            </ol>
                        </div>

                        <!-- Formulario -->
                        <form action="{{ route('importacion.importar') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf

                            <!-- Selector de archivo -->
                            <div>
                                <label for="archivo" class="block text-sm font-medium text-gray-700 mb-2">
                                    Archivo CSV <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="archivo" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            <p class="mb-2 text-sm text-gray-500">
                                                <span class="font-semibold">Click para seleccionar</span> o arrastra el archivo
                                            </p>
                                            <p class="text-xs text-gray-500">CSV (MAX. 10MB)</p>
                                            <p id="file-name" class="mt-2 text-sm text-blue-600 font-semibold"></p>
                                        </div>
                                        <input id="archivo" name="archivo" type="file" accept=".csv,.txt" class="hidden" required 
                                            onchange="document.getElementById('file-name').textContent = this.files[0]?.name || ''">
                                    </label>
                                </div>
                            </div>

                            <!-- Fecha de carga (opcional) -->
                            <div>
                                <label for="fecha_carga" class="block text-sm font-medium text-gray-700 mb-2">
                                    Fecha de Carga (opcional)
                                </label>
                                <input type="date" name="fecha_carga" id="fecha_carga" 
                                    value="{{ date('Y-m-d') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Por defecto: Hoy. Cambia solo si el archivo corresponde a otra fecha.</p>
                            </div>

                            <!-- Información sobre duplicados -->
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <h4 class="font-semibold text-yellow-900 mb-2">Manejo de Duplicados:</h4>
                                <p class="text-sm text-yellow-800">
                                    El sistema identifica registros duplicados por: <strong>RUT + RBD + Fecha de Carga</strong>.
                                    Si un registro ya existe, se actualizará con los nuevos datos en lugar de crear uno duplicado.
                                </p>
                            </div>

                            <!-- Botones -->
                            <div class="flex justify-end space-x-3">
                                <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    Importar Archivo
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

                <!-- Panel Lateral -->
                <div class="space-y-6">
                    
                    <!-- Ayuda -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">📥 Recursos</h3>
                            
                            <div class="space-y-3">
                                <a href="{{ route('importacion.plantilla') }}" 
                                    class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition">
                                    <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-green-900">Descargar Plantilla</p>
                                        <p class="text-xs text-green-700">Archivo CSV de ejemplo</p>
                                    </div>
                                </a>

                                <a href="https://sige.mineduc.cl/Sige/Login" target="_blank"
                                    class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                                    <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-blue-900">Ir a SIGE</p>
                                        <p class="text-xs text-blue-700">Descargar datos oficiales</p>
                                    </div>
                                </a>

                                <a href="{{ route('docentes.index') }}"
                                    class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                                    <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Ver Docentes</p>
                                        <p class="text-xs text-gray-700">Lista completa</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas Rápidas -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Estadísticas</h3>
                            
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Docentes</dt>
                                    <dd class="text-2xl font-bold text-gray-900">{{ \App\Models\DocenteIdoneidad::count() }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Última Importación</dt>
                                    <dd class="text-sm text-gray-900">
                                        @php
                                            $ultima = \App\Models\DocenteIdoneidad::latest('created_at')->first();
                                        @endphp
                                        {{ $ultima ? $ultima->created_at->diffForHumans() : 'N/A' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Historial de Importaciones -->
            @if($historial->count() > 0)
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Historial de Importaciones</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Carga</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">RBD</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Institución</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registros</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($historial as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($item->fecha_carga)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $item->rbd }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $item->nombre_esta }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $item->total }} docentes
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>