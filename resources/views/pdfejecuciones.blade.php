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
        <p class="header-title">Total a Pagar/Cobrar Por Sentencia En Ejecución</p>
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
                <th>Monto de Ejecución 1</th>
                <th>Monto de Ejecución 2</th>
                <th>Interés 1</th>
                <th>Interés 2</th>
                <th>Costos</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sumaEjecucion1 = 0;
                $sumaEjecucion2 = 0;
                $sumaInteres1 = 0;
                $sumaInteres2 = 0;
                $sumaCostos = 0;
                $sumaTotal = 0;
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
                    <td>
                        {{ !empty($expediente['monto_ejecucion1']) ? $expediente['monto_ejecucion1'] : '0.00' }}
                    </td>
                    <td>
                        {{ !empty($expediente['monto_ejecucion2']) ? $expediente['monto_ejecucion2'] : '0.00' }}
                    </td>
                    <td>
                        {{ !empty($expediente['interes1']) ? $expediente['interes1'] : '0.00' }}
                    </td>
                    <td>
                        {{ !empty($expediente['interes2']) ? $expediente['interes2'] : '0.00' }}
                    </td>
                    <td>
                        {{ !empty($expediente['costos']) ? $expediente['costos'] : '0.00' }}
                    </td>


                    <td>
                        @php
                            $ejecucion1 = floatval($expediente['monto_ejecucion1'] ?? '0.00');
                            $ejecucion2 = floatval($expediente['monto_ejecucion2'] ?? '0.00');
                            $interes1 = floatval($expediente['interes1'] ?? '0.00');
                            $interes2 = floatval($expediente['interes2'] ?? '0.00');
                            $costos = floatval($expediente['costos'] ?? '0.00');
                            $total = $ejecucion1 + $ejecucion2 + $interes1 + $interes2 + $costos;
                            $sumaEjecucion1 += $ejecucion1;
                            $sumaEjecucion2 += $ejecucion2;
                            $sumaInteres1 += $interes1;
                            $sumaInteres2 += $interes2;
                            $sumaCostos += $costos;
                            $sumaTotal += $total;
                        @endphp
                        {{ number_format($total, 2)  }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

</body>

</html>