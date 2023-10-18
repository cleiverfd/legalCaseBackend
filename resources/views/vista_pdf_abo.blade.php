<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }

        h1 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        td.correo {
            font-size: 10px; /* Adjust the font size as needed */
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            h1 {
                page-break-before: always;
            }

            table {
                page-break-inside: auto;
            }

            th, td {
                padding: 6px;
            }
        }
    </style>
</head>
<body>

    <h1>ABOGADOS</h1>

    <table>
        <thead>
            <tr>
                <th>DNI</th>
                <th>NOMBRES</th>
                <th>TELEFONO</th>
                <th>CORREO</th>
                <th>CARGA LABORAL</th>
                <!-- <th>DISPONIBILIDAD</th> -->
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                    <td>{{ $item['persona']['nat_dni'] ?? '' }}</td>
                    <td>{{ $item['persona']['nat_nombres'] ?? '' }} {{ $item['persona']['nat_apellido_paterno'] ?? '' }} {{ $item['persona']['nat_apellido_materno'] ?? '' }}</td>
                    <td>{{ $item['persona']['nat_telefono'] ?? '' }}</td>
                    <td class="correo">{{ $item['persona']['nat_correo'] ?? '' }}</td>
                    <td>{{ $item['abo_carga_laboral'] ?? '' }}</td>
                    <!-- <td>{{ $item['abo_disponibilidad'] ?? '' }}</td> -->
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>

