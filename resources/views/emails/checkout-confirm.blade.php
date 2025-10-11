<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation commande {{ $order->numero_commande }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5; color: #333333; max-width: 600px; margin: 0 auto; padding: 25px 20px; background-color: #f5f5f5;">
    <!-- En-tête -->
    <div style="background: white; padding: 25px; border-radius: 8px 8px 0 0; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <div style="text-align: center; margin-bottom: 15px;">
            <!-- Logo optionnel -->
            <!-- <img src="logo.png" alt="Paradis Déco" style="max-height: 60px;"> -->
            <h2 style="color: #2a4365; font-size: 22px; margin: 10px 0 5px;">Confirmation de commande #{{ $order->numero_commande }}</h2>
            <p style="color: #4a5568; font-size: 15px; margin: 5px 0;">{{ now()->format('d/m/Y') }}</p>
        </div>

        <p style="margin: 15px 0 5px;">Bonjour {{ $client->name }},</p>
        <p style="color: #2b6cb0; font-style: italic; margin: 5px 0 15px;">Merci pour votre confiance et votre achat chez Paradis Déco !</p>
    </div>

    <!-- Récapitulatif de commande -->
    <div style="background: white; padding: 20px 25px; margin: 5px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <h3 style="color: #2a4365; font-size: 18px; margin: 0 0 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Détails de votre commande</h3>

        <ul style="list-style: none; padding: 0; margin: 0;">
            @foreach($order->items as $item)
            <li style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #e2e8f0;">
                <span style="flex: 2;">{{ $item->product->name }}</span>
                <span style="flex: 1; text-align: center;">× {{ $item->quantity }}</span>
                <span style="flex: 1; text-align: right;">{{ number_format($item->subtotal, 2, ',', ' ') }} TND</span>
            </li>
            @endforeach
        </ul>
    </div>

    <!-- Total et frais -->
    <div style="background: white; padding: 20px 25px; margin: 5px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; margin: 5px 0;">
            <span>Sous-total HT:</span>
            <span>{{ number_format($order->subtotal_ht, 2, ',', ' ') }} TND</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin: 5px 0;">
            <span>Frais de livraison:</span>
            <span>{{ number_format($order->shipping_cost, 2, ',', ' ') }} TND</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin: 15px 0 5px; font-weight: bold; font-size: 17px; color: #2a4365;">
            <span>Total TTC:</span>
            <span>{{ number_format($order->subtotal_ht + $order->shipping_cost, 2, ',', ' ') }} TND</span>
        </div>
    </div>

    <!-- Informations livraison -->
    <div style="background: white; padding: 20px 25px; margin: 5px 0 15px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <h3 style="color: #2a4365; font-size: 18px; margin: 0 0 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Suivi de commande</h3>
        <p style="margin: 10px 0;">Votre commande est en cours de préparation. Nous vous informerons par email dès que votre colis sera expédié.</p>
        <p style="margin: 10px 0;">Pour toute question, vous pouvez répondre à cet email ou nous contacter au 12 345 678.</p>
    </div>

    <!-- Pied de page -->
    <div style="text-align: center; color: #718096; font-size: 13px; margin-top: 25px;">
        <p style="margin: 5px 0;">À bientôt,<br><strong style="color: #2b6cb0;">L'équipe Paradis Déco</strong></p>
        <p style="margin: 15px 0 5px; font-size: 12px;">Cet email est envoyé automatiquement, merci de ne pas y répondre directement.</p>
        <p style="margin: 5px 0; font-size: 12px;">© {{ date('Y') }} Paradis Déco. Tous droits réservés.</p>
    </div>
</body>
</html>
