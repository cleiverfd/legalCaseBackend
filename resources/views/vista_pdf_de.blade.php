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

<h1>DEMANDANTES</h1>
<table>
    <thead>
        <tr>
            <th>Documento</th>
            <th>Nombres / Razón Social</th>
            <th>Dirección</th>
            <th>Correo</th>
        </tr>
    </thead>
    <tbody>
        @php
            $personasMostradasNaturales = [];
            $personasMostradasJuridicas = [];
        @endphp

        @foreach ($data as $expediente)
            @php
                $tipoPersona = $expediente['type'];
                $personaData = $expediente['person_data'];
                $direccion = $expediente['person']['address']['dir_calle_av'];
            @endphp

            @if ($tipoPersona == 'natural' && !in_array($personaData['nat_id'], $personasMostradasNaturales))
                @php
                    $personasMostradasNaturales[] = $personaData['nat_id'];
                @endphp
                <tr>
                    <td>{{ $personaData['nat_dni'] }}</td>
                    <td>{{ $personaData['nat_apellido_paterno'] }} {{ $personaData['nat_apellido_materno'] }}, {{ $personaData['nat_nombres'] }}</td>
                    <td>{{ $direccion }}</td>
                    <td>{{ $personaData['nat_correo'] }}</td>
                </tr>
            @elseif ($tipoPersona == 'juridica' && !in_array($personaData['jur_id'], $personasMostradasJuridicas))
                @php
                    $personasMostradasJuridicas[] = $personaData['jur_id'];
                @endphp
                <tr>
                    <td>{{ $personaData['jur_ruc'] }}</td>
                    <td>{{ $personaData['jur_razon_social'] }}</td>
                    <td>{{ $direccion }}</td>
                    <td>{{ $personaData['jur_correo'] }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>


</body>
</html>

