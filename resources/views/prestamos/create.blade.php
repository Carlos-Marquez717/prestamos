@extends('layouts.app')

@section('content')
<br><br><br>
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto bg-black p-4 rounded-lg shadow-md">
            <h1 class="text-xl font-bold mb-4 text-center text-white">Agregar Préstamo</h1>

            @if ($errors->any())
                <div class="bg-red-100 text-red-800 p-2 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('prestamos.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="nombre_cliente" class="block text-sm font-medium text-white">Cliente</label>
                    <select name="nombre_cliente" id="nombre_cliente"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white text-black">
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->nombre }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="cantidad_prestamo" class="block text-sm font-medium text-white">Cantidad del
                        Préstamo</label>
                    <input type="number" name="cantidad_prestamo" id="cantidad_prestamo"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white text-black">
                </div>

                <div>
                    <label for="fecha" class="block text-sm font-medium text-white">Fecha del Préstamo</label>
                    <input type="date" name="fecha" id="fecha"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white text-black">
                </div>

                <div>
                    <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
