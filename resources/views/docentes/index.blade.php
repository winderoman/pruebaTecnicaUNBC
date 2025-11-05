<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Idoneidad Docente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtros de Búsqueda</h3>
                    
                    <form method="GET" action="{{ route('docentes.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            
                            <!-- Filtro por RUT -->
                            <div>
                                <label for="rut" class="block text-sm font-medium text-gray-700">RUT</label>
                                <input type="text" name="rut" id="rut" value="{{ request('rut') }}" 
                                    placeholder="12345678-9"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <!-- Filtro por Nombre -->
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                                <input type="text" name="nombre" id="nombre" value="{{ request('nombre') }}" 
                                    placeholder="Buscar por nombre..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <!-- Filtro por RBD -->
                            <div>
                                <label for="rbd" class="block text-sm font-medium text-gray-700">Institución (RBD)</label>
                                <select name="rbd" id="rbd" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Todas</option>
                                    @foreach($instituciones as $inst)
                                    <option value="{{ $inst->rbd }}" {{ request('rbd') == $inst->rbd ? 'selected' : '' }}>
                                        {{ $inst->rbd }} - {{ $inst->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtro por Estado -->
                            <div>
                                <label for="estado_idoneidad" class="block text-sm font-medium text-gray-700">Estado Idoneidad</label>
                                <select name="estado_idoneidad" id="estado_idoneidad" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Todos</option>
                                    @foreach($estadosIdoneidad as $estado)
                                    <option value="{{ $estado }}" {{ request('estado_idoneidad') == $estado ? 'selected' : '' }}>
                                        {{ $estado }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtro por Función -->
                            <div>
                                <label for="funcion_principal" class="block text-sm font-medium text-gray-700">Función</label>
                                <input type="text" name="funcion_principal" id="funcion_principal" value="{{ request('funcion_principal') }}" 
                                    placeholder="Ej: DOCENTE DE AULA"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <!-- Filtro por Fecha -->
                            <div>
                                <label for="fecha_desde" class="block text-sm font-medium text-gray-700">Fecha Carga (Desde)</label>
                                <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('docentes.index') }}" 
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Limpiar
                            </a>
                            <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Resultados -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Resultados: {{ $docentes->total() }} docentes encontrados
                        </h3>
                    </div>

                    <!-- Tabla de Resultados -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        RUT
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nombre Completo
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Función
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Horas
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($docentes as $docente)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $docente->rut_completo }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $docente->nombre_completo }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $docente->funcion_principal }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $docente->horas_contrato }}h
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $docente->estado_idoneidad == 'OK' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $docente->estado_idoneidad }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('docentes.show', $docente) }}" class="text-blue-600 hover:text-blue-900">
                                            Ver detalle
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No se encontraron docentes con los filtros seleccionados.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $docentes->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>