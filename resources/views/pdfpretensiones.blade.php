<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Lista de Expedientes</title>
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
        <p class="header-title">Total de Pretensiones en Demanda</p>
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
                <th>Demandado</th>
                <th>Estado</th>
                <th>Pretensión</th>
                <th>Monto de Pretensión</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sumaPretensionDemandante = 0;
                $sumaPretensionDemandado = 0;
                $sumaPretensionTotal = 0;
            @endphp

            @foreach($data as $expediente)
                <tr>
                    <td>{{ $expediente['numero'] ?? '' }}</td>
                    <td>{{ $expediente['fecha_inicio'] ?? ''}}</td>
                    <td>
                        @if($expediente['procesal'] === 'demandante')
                            @if($expediente['tipo_persona'] === 'natural')
                                {{ ucwords(strtolower($expediente['nombres'] ?? '')) . ' ' . ucwords(strtolower($expediente['apellido_paterno'] ?? '')) . ' ' . ucwords(strtolower($expediente['apellido_materno'] ?? '')) }}
                            @else
                                {{ ucwords(strtolower($expediente['razon_social'] ?? '')) }}
                            @endif
                        @else
                            Unprg
                        @endif
                    </td>
                    <td>
                        @if($expediente['procesal'] !== 'demandante')
                            @if($expediente['tipo_persona'] === 'natural')
                                {{ ucwords(strtolower($expediente['nombres'] ?? '')) . ' ' . ucwords(strtolower($expediente['apellido_paterno'] ?? '')) . ' ' . ucwords(strtolower($expediente['apellido_materno'] ?? '')) }}
                            @else
                                {{ ucwords(strtolower($expediente['razon_social'] ?? '')) }}
                            @endif
                        @else
                            Unprg
                        @endif
                    </td>
                    <td>{{$expediente['estado_proceso'] ?? ''}}</td>
                    <td>{{ $expediente['pretencion'] ?? '' }}</td>
                    <td>{{ $expediente['monto_pretencion'] ?? '' }}</td>
                </tr>
                
                @php
                    $montoPretencion = floatval($expediente['monto_pretencion']);
                    if ($expediente['procesal'] === 'demandante') {
                        $sumaPretensionDemandante += $montoPretencion;
                    } else {
                        $sumaPretensionDemandado += $montoPretencion;
                    }
                    $sumaPretensionTotal += $montoPretencion;
                @endphp
            @endforeach
        </tbody>
    </table>
    <p>Suma de Pretensiones de Demandantes: {{ number_format($sumaPretensionDemandante,2) }}</p>
    <p>Suma de Pretensiones de Demandados: {{ number_format($sumaPretensionDemandado,2) }}</p>
    <p>Pretensión Total: {{ number_format($sumaPretensionTotal,2) }}</p>
@endif


</body>

</html>