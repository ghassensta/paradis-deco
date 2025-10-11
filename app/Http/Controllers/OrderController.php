<?php

namespace App\Http\Controllers;

use App\Models\Statut;
use App\Models\Client;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
  public function index()
    {
        $statuts = Statut::all();
        $clients = Client::get();
        return view('admin.orders.index', compact('statuts', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:users,id',
            'statut_id' => 'required|exists:statuts,id',
            'notes' => 'nullable|string',
        ]);

        $order = Order::create([
            'numero_commande' => 'ORD-' . time(),
            'client_id' => $validated['client_id'],
            'statut_id' => $validated['statut_id'],
            'notes' => $validated['notes'],
            'subtotal' => 0,
            'total' => 0,
        ]);

        return response()->json(['success' => true, 'order' => $order]);
    }

    public function edit($id)
    {
        $order = Order::with(['client', 'statut'])->findOrFail($id);
        $statuts = Statut::all();
        $clients = Client::get();
        return view('admin.commandes.edit', compact('order', 'statuts', 'clients'));
    }



    public function editStatus($id)
    {
        $order = Order::findOrFail($id);
        $statuts = Statut::all();
        return view('admin.orders.edit-status', compact('order', 'statuts'));
    }

   public function updateStatus(Request $request, $id)
{
    // Récupération de la commande ou 404 automatique
    $order = Order::findOrFail($id);

    // Validation de la requête
    $validated = $request->validate([
        'statut_id' => ['required', 'integer', 'exists:statuts,id'],
    ]);

    // Mise à jour du statut
    $order->statut_id = $validated['statut_id'];

    // Si le statut correspond à « payée » (ID = 3) et que paid_at n’est pas encore renseigné
    if ((int) $validated['statut_id'] === 3 && is_null($order->paid_at)) {
        $order->paid_at = now();   // enregistre la date/heure actuelle
    }

    // Sauvegarde des modifications
    $order->save();

    return response()->json(['success' => true]);
}


    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(['success' => true]);
    }

    public function pdf($id)
    {
        $order = Order::with(['client', 'statut', 'items.product'])->findOrFail($id);
        $pdf = PDF::loadView('admin.commandes.pdf', compact('order'));
        return $pdf->download('commande-' . $order->numero_commande . '.pdf');
    }
}
