<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>NOTA DE ENTREGA</title>
    <style>
        /* Configuración de página para PDF */
        @page { margin: 1cm; }
        
        body { 
            font-family: 'Helvetica', Arial, sans-serif; 
            font-size: 11px; 
            color: #333; 
            line-height: 1.5;
            margin: 0;
        }

        /* Encabezado con Flex-like layout (usando tablas para mejor compatibilidad PDF) */
        .header-table { width: 100%; border-bottom: 2px solid #444; margin-bottom: 20px; padding-bottom: 10px; }
        .header-logo { width: 50%; }
        .header-info { width: 50%; text-align: right; }
        
        h1 { margin: 0; color: #444; font-size: 18px; text-transform: uppercase; }
        .text-muted { color: #777; }

        /* Estilos de Tabla */
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th { 
            background-color: #f2f2f2; 
            color: #444; 
            font-weight: bold; 
            padding: 10px; 
            border-bottom: 2px solid #ddd;
            text-align: left;
        }
        .table td { padding: 10px; border-bottom: 1px solid #eee; }
        
        /* Alineaciones */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-nombre {text-transform: capitalize;}
        /* Contenedor Total */
        .total-container { 
            margin-top: 30px; 
            width: 100%; 
        }
        .total-box { 
            float: right; 
            width: 250px; 
            padding: 15px; 
            border-radius: 5px;
            background: #f9f9f9; 
        }
            
        .total-row { font-size: 14px; font-weight: bold; color: #000; }
        
        .footer { 
            position: fixed; 
            bottom: 0; 
            width: 100%; 
            text-align: center; 
            font-size: 9px; 
            color: #aaa;
        }
    </style>
</head>
<body>
    @php
        // 1. Establecer la zona horaria a Caracas
        date_default_timezone_set('America/Caracas');
        // 2. Obtener la fecha actual con el formato día/mes/año y hora
        $fecha_formateada = date('d/m/Y h:i:s A'); // Formato 24 horas
    @endphp

    <table class="header-table">
        <tr>
            <td class="header-logo">
                @if($logo)
                    <img src="{{ $logo }}" alt="LOGO SEA SWIMWEAR" style="width: 160px;">
                @endif
            </td>
            <td class="header-info">
                <h1>Nota de Entrega</h1>
                <p>
                    <strong>Número:</strong> #{{ $order["orden_id"] }}<br>
                    <strong>Fecha:</strong> {{ $fecha_formateada }}
                </p>
            </td>
        </tr>
    </table>

    <div style="margin-bottom: 20px;">
        <strong>Información:</strong><br>
        <span class="text-muted">C.I:</span> 
        {{ $order["cliente"]->cedula }},
        <span class="text-muted">Nombres/Apellidos:</span> 
        <span class="text-nombre">{{ $order["cliente"]->names }}</span>
        <br>
        <span class="text-muted">Teléfono:</span> 
        <span class="text-nombre">{{ $order["cliente"]->phone }},</span>
        <span class="text-muted">Dirección:</span>
        <span class="text-nombre">{{ $order["cliente"]->address }}</span>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Descrip. Producto. (Color/ talla / Estampado)</th>
                <th class="text-center" style="width: 80px;">Cant.</th>
                <th class="text-right" style="width: 100px;">Precio Unit.</th>
                <th class="text-right" style="width: 100px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order["productos"] as $item)
            <tr>
                <td>
                    <table style="width: 100%; border: none; border-collapse: collapse;">
                        <tr>
                            <td style="width: 55px; border: none; padding: 0; vertical-align: middle;">
                                <img src="{{ $item['img'] }}" alt="SEA SWIMWEAR" style="width: 50px; border-radius: .8rem; display: block;">
                            </td>
                            <td style="border: none; padding: 0 0 0 10px; vertical-align: middle;">
                                <span style="text-transform: capitalize; font-weight: bolder; display: block; line-height: 1;">
                                    {{ $item["nombre"] }}
                                </span>
                                <span class="text-muted" style="font-size: .6rem;">
                                    (
                                    @foreach ($item["sku"] as $p)
                                        <span>{{ $p }}</span>{{ !$loop->last ? ' / ' : '' }}
                                    @endforeach
                                    )
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="text-center">{{ $item["cantidad"] }}</td>
                <td class="text-right">${{ number_format($item["precio"], 2) }}</td>
                <td class="text-right">${{ number_format($item["precio"] * $item["cantidad"], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="total-container">
        <div class="total-box">
            <table style="width: 100%;">
                <tr>
                    <td class="text-left" style="color: green;">Monto Pagado:</td>
                    <td class="text-right" style="color: green;">${{ number_format($order["orden_pago_total"], 2) }}</td>
                </tr>
                <tr>
                    <td class="text-left" style="color: red;">Monto Pendiente:</td>
                    <td class="text-right" style="color: red;">${{ number_format($order["orden_monto_restante"], 2) }}</td>
                </tr>
                <tr class="total-row" >
                    <td class="text-left" style="border-top: #777 solid 1px;">COMPRA TOTAL:</td>
                    <td class="text-right" style="border-top: #777 solid 1px;">${{ number_format($order["orden_compra_total"], 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer">
        Documento generado automáticamente - Gracias por su preferencia
        SEA SWIMWEAR. Todos los derechos reservados.
    </div>
</body>
</html>
