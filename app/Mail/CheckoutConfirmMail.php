<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CheckoutConfirmMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}   // ⚑ on injecte la commande

    /**
     * Définition de l’enveloppe (sujet, tags, etc.)
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation de votre commande '.$this->order->numero_commande,
            tags:    ['order', 'confirmation'],
            metadata: ['order_id' => (string) $this->order->id],
        );
    }

    /**
     * Vue + variables envoyées au template
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.checkout-confirm',          // resources/views/emails/checkout-confirm.blade.php
            with: [
                'order'  => $this->order,
                'client' => $this->order->client,
            ],
        );
    }

    /**
     * Pièces jointes éventuelles
     */
    public function attachments(): array
    {
        return [
            // Attachment::fromPath(storage_path('invoices/'.$this->order->pdf)),
        ];
    }
}
