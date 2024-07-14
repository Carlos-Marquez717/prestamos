@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection



@section('content')
    <br><br><br><br>
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto bg-black p-4 rounded-lg shadow-md">
            <h1 class="text-xl font-bold mb-4 text-center text-white">Agregar Abono</h1>

            @if ($errors->any())
                <div class="bg-red-100 text-red-800 p-2 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('abonos.store', $prestamo) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="monto" class="block text-sm font-medium text-white">Monto</label>
                    <input type="number" name="monto" id="monto"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white text-black"
                        value="{{ old('monto') }}" required>
                    @error('monto')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="fecha" class="block text-sm font-medium text-white">Fecha del Abono</label>
                    <input type="text" name="fecha" id="fecha"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white text-black"
                        value="{{ old('fecha') }}" required placeholder="dd-mm-yyyy">
                    @error('fecha')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Agregar Abono
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        // Script para limpiar el formulario después de enviar
        document.getElementById('abonoForm').addEventListener('submit', function() {
            // No limpiamos los campos aquí para asegurar que se envíen al servidor
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#fecha').datepicker({
                dateFormat: 'dd-mm-yy'
            });
        });
    </script>
@endsection
