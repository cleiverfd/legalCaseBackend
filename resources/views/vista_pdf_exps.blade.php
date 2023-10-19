<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listado de Expedientes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }

        h1 {
            color: #333;
        }

        table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 2px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px; /* Ajusta el tamaño de la letra según sea necesario */
        }
    </style>
</head>
<body>

    <h1> Expedientes</h1>

    @if(empty($data))
        <p>No hay datos disponibles</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Fecha de Inicio</th>
                    <th>Demandante</th>
                    <th>Documento</th>
                    <th>Materia</th>
                    <th>Estado</th>
                    <th>Especialidad / Instancia / Distrito Judicial</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $expediente)
                    <tr>
                        <td>{{ $expediente['exp_numero'] ?? '' }}</td>
                        <td>{{ $expediente['exp_fecha_inicio'] ?? ''}}</td>
                        <td>{{ $expediente['type'] === 'natural' ? ($expediente['person_data']['nat_nombres'] ?? '') . ' ' . ($expediente['person_data']['nat_apellido_paterno'] ?? '') . ' ' . ($expediente['person_data']['nat_apellido_materno'] ?? '') : ($expediente['person_data']['jur_razon_social'] ?? '') }}</td>
                        <td>{{ $expediente['type'] === 'natural' ? ($expediente['person_data']['nat_dni'] ?? '') : 'RUC: ' . ($expediente['person_data']['jur_ruc'] ?? '') }}</td>
                        <td>{{ $expediente['exp_materia'] ?? ''}}</td>
                        <td>{{ $expediente['exp_estado_proceso'] ?? ''}}</td>
                        <td>{{ $expediente['specialty']['esp_nombre']  ?? ''}} 
                            / {{ $expediente['specialty']['instance']['ins_nombre']  ?? ''}}
                            / {{ $expediente['specialty']['instance']['judicialdistrict']['judis_nombre'] ?? ''}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>
</html>
