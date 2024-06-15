<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
           
            color: rgb(10, 10, 10);
         
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            
            text-align: center;
        }
      
        .logo img {
            display: block;
            margin: 0 auto 0px auto;
            max-width: 100px; /* Ajustar el tamaño del logo */
        }
        h1 {
            margin: 0 0 20px 0;
            
           
            color: white;
            border-radius: 5px;
            text-align: center;
        }
        table {
            width: 100%;
   
        }
    

        .title-cell {
            background-color: black;
            color: white;
            border: 1px solid white;
        }

        .title {
           
            color: black;
           
        }
    </style>
</head>
<body>
    <div class="container">

        
        <table>
            
            
                
                
            
            <tr>
                <th class="title-cell">Cliente</th>
                <td class="title-cell">{{ $abono->prestamo->cliente->nombre }}</td>
            </tr>
            <tr>
                <th class="title-cell">cantidad prestamo</th>
                <td class="title-cell">${{ $abono->prestamo->cantidad_prestamo  }}</td>
            </tr>
         
            <tr>
                <th class="title-cell">Abonos</th>
                <td class="title-cell">{{ $abono->fecha }}</td>
            </tr>
            <tr>
                <th class="title-cell">Abonos</th>
                <td class="title-cell">${{ $abono->monto }}</td>
            </tr>

            <tr>
                <th class="title-cell">Restante</th>
                <td class="title-cell">${{ $abono->prestamo->cantidad_prestamo - $abono->prestamo->abonos()->sum('monto') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
