<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;         
use App\Models\Order;

class PdfService
{
    public function generarFactura($orderID)
    {
        // 1. Obtener la orden con sus relaciones (Tu lógica impecable)
        $order = Order::with([
            'client:cedula,names,phone,address',
            'items:id,order_id,product_variant_id,quantity,applied_unit_price',
            'items.orderVariante:id,product_id,full_sku,image_id',
            'items.orderVariante.image:id,main_image_url',
            'items.orderVariante.product:id,name'
        ])->findOrFail($orderID);

        // 2. Organizar los datos (Mantenemos tu lógica de procesamiento)
        $organizedOrder = [
            'orden_id'            => $order->id,
            'orden_compra_total'   => $order->total_purchase,
            'orden_pago_total'      => $order->total_paid,
            'orden_monto_restante'  => $order->remaining_amount,
            'cliente'               => $order->client,
            'productos'             => $order->items->map(function ($item) {
                
                $fullSku = explode('-', trim($item->orderVariante->full_sku ?? ''));
                $propiedades = array_slice($fullSku, -3);

                // Lógica de imagen Base64 (Muy bien hecho para DomPDF)
                $imgBase64 = null;
                $imagePath = $item->orderVariante->image->main_image_url ?? null;

                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    $fileContent = Storage::disk('public')->get($imagePath);
                    $type = pathinfo($imagePath, PATHINFO_EXTENSION);
                    $imgBase64 = 'data:image/' . $type . ';base64,' . base64_encode($fileContent);
                }

                return [
                    'item_id'  => $item->id,
                    'cantidad' => $item->quantity,
                    'precio'   => $item->applied_unit_price,
                    'sku'      => $propiedades,
                    'nombre'   => $item->orderVariante->product->name ?? 'Producto no encontrado',
                    'img'      => $imgBase64 ?? null,
                ];
            }),
        ];

        // Lógica del Logo
        $logoPath = public_path('images/SEA-SWINWEAR-LOGO.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
        }
        
        // 3. Generar el PDF
        return Pdf::loadView('pdfs.ordenCliente', [
            'order' => $organizedOrder,
            'logo'  => $logoBase64,
        ]);
    }
}
