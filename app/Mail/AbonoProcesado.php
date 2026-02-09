<?php

namespace App\Mail;

use App\Models\Order;          // Importa del Modelo de la Orden
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbonoProcesado extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    // Propiedades públicas disponibles en la vista
    public function __construct(
        public Order $order, 
        public $reporte
    ) {}

    // Configuración de reintentos en la misma clase
    public $tries = 3; // Número de intentos antes de fallar definitivamente
    public $backoff = 60; // Espera 60 segundos antes de reintentar si falla
    
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pago Verificado',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.abono-procesado',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
