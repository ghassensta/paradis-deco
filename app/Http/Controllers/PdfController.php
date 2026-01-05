<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\Companie;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PdfController extends Controller
{
  public function generatePDF(int $id)
{
    try {
        // Fetch the order with related data
        $commande = Order::with([
            'client:id,name,adresse,phone',
            'statut:id,name',
            'items.product:id,name',
        ])->findOrFail($id);

        // Prepare data for the PDF
        $data = [
            'numero_commande' => $commande->numero_commande,
            'date' => $commande->created_at ? $commande->created_at->format('d/m/Y') : 'N/A',
            'client' => $commande->client->name ?? 'N/A',
            'adresse' => $commande->client->adresse ?? 'N/A',
            'statut' => $commande->statut->name ?? 'N/A',
            'items' => $commande->items,
            'subtotal_ht' => $commande->subtotal_ht ?? 0,
            'shipping_cost' => $commande->shipping_cost ?? 0,
            'tax_rate' => $commande->tax_rate ?? 19, // Use database tax rate
            'tax_tva' => $commande->tax_tva ?? 0,
            'total_ttc' => $commande->total_ttc ?? 0,
            'phone' => $commande->client->phone ?? 'N/A',
            'shipped_at' => $commande->shipped_at ? $commande->shipped_at->format('d/m/Y') : 'N/A',
            'paid_at' => $commande->paid_at ? $commande->paid_at->format('d/m/Y') : 'N/A',
            'currency' => 'DT', // Dynamic currency
            'logo_path' => public_path('images/logo.png'), // Path to company logo
        ];

        // Generate and stream the PDF
        return Pdf::loadView('pdf.facture', ['data' => $data])
            ->stream("facture_{$commande->numero_commande}.pdf");
    } catch (\Exception $e) {
        // Log the error and return a user-friendly response
        \Log::error('PDF generation failed: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to generate PDF'], 500);
    }
}

}
