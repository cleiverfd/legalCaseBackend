<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Lista completa de Expedientes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            /* background-color: #007bff; */
            /* padding: 20px 0; */
        }

        .header-title {
            color: #000;
            font-size: 24px;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table {
            border: 0.5px solid #ddd;
        }

        th {
            border: 0.5px solid #fff;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }

        td {
            border: 0.5px solid #ddd;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }

        th {
            /* background-color: #007bff; */
            background-color: #e5e5e5;
            color: #000;
        }

        th:first-child {
            border-left: none;
            /* Elimina el borde izquierdo en la primera columna del encabezado */
        }

        th:last-child {
            border-right: none;
            /* Elimina el borde derecho en la última columna del encabezado */
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>

    <div class="header">
        <p class="header-title">Lista completa de Expedientes</p>
    </div>

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
                <th>Especialidad</th>
                <!-- <th>Especialidad / Instancia / Distrito Judicial</th> -->
            </tr>
        </thead>
        <tbody>
            @foreach($data as $expediente)
            <tr>
                <td>{{ $expediente['exp_numero'] ?? '' }}</td>
                <td>{{ $expediente['exp_fecha_inicio'] ?? '' }}</td>
                <td>
                    @if ($expediente['type'] === 'natural')
                    {{ ucwords(strtolower($expediente['person_data']['nat_nombres'] ?? '')) }}
                    {{ $expediente['person_data']['nat_apellido_paterno'] ? ucwords(strtolower($expediente['person_data']['nat_apellido_paterno'])) : '' }}
                    {{ $expediente['person_data']['nat_apellido_materno'] ? ucwords(strtolower($expediente['person_data']['nat_apellido_materno'])) : '' }}
                    @else
                    {{ $expediente['person_data']['jur_razon_social'] ?? '' }}
                    @endif
                </td>
                <td>
                    @if ($expediente['type'] === 'natural')
                    {{ $expediente['person_data']['nat_dni'] ?? '' }}
                    @else
                    RUC: {{ $expediente['person_data']['jur_ruc'] ?? '' }}
                    @endif
                </td>
                <td>{{ ucwords(strtolower($expediente['exp_materia'] ?? '')) }}</td>
                <td>{{ ucwords(strtolower($expediente['exp_estado_proceso'] ?? '')) }}</td>
                <td>{{ ucwords(strtolower($expediente['specialty']['esp_nombre'] ?? '')) }}</td>

                <!-- <td>{{ $expediente['specialty']['esp_nombre']  ?? ''}}
                    / {{ $expediente['specialty']['instance']['ins_nombre']  ?? ''}}
                    / {{ $expediente['specialty']['instance']['judicialdistrict']['judis_nombre'] ?? ''}}</td> -->
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

</body>

</html>