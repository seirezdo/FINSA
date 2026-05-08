<x-app-layout>
    @if(auth()->user()->role !== \App\Enums\UserRole::CLIENTE)
    <button class="bg-blue-600 text-white px-4 py-2 rounded">
        Gestión Administrativa
    </button>
@endif
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <h2 class="text-2xl font-bold mb-4">Registrar Nuevo Cliente</h2>
                <form action="{{ route('clientes.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="w-full border-gray-300 rounded-md" required>
                        </div>
                        <div>
                            <label>Apellido Paterno</label>
                            <input type="text" name="apellido_paterno" class="w-full border-gray-300 rounded-md" required>
                        </div>
                         <div>
                            <label>Apellido Materno</label>
                            <input type="text" name="apellido_materno" class="w-full border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label>N° Documento (CURP/INE)</label>
                            <input type="text" name="numero_documento" class="w-full border-gray-300 rounded-md" required>
                        </div>
                        <div>
                            <label>Perfil de Riesgo</label>
                            <select name="perfil_riesgo" class="w-full border-gray-300 rounded-md">
                                <option value="bajo">Bajo</option>
                                <option value="medio">Medio</option>
                                <option value="alto">Alto</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label>Fecha de Registro</label>
                            <input type="date" name="fecha_registro" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded shadow">Guardar Cliente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>