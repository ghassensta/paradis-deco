<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Facture #{{ $data['numero_commande'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.6;
            padding: 20px;
        }
        .invoice-print {
            max-width: 21cm;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 7px;
            padding-bottom: 20px;
            margin-left: 5%;
        }
        .company-info,
        .invoice-info {
            display: table-cell;
            vertical-align: top;
            width: 50%;
            font-size: 13px;
            line-height: 1.5;
        }
        .company-info img {
            max-width: 60px;
            margin-bottom: 10px;
        }
        .invoice-info h1 {
            color: #005566;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .invoice-info p,
        .company-info p {
            margin: 4px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th,
        table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        table th {
            background-color: #005566;
            color: #fff;
            font-weight: bold;
        }
        table tr:nth-child(even) {
            background-color: #f8f8f8;
        }
        .total-row {
            font-weight: bold;
            background-color: #e6f0f5;
        }
        .total {
            text-align: right;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
            color: #005566;
        }
        .footer {
            border-top: 2px solid #ddd;
            padding-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 40px;
        }
        .terms-conditions {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #f8f8f8;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
            text-align: justify;
        }
        .terms-conditions h3 {
            color: #005566;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: left;
        }
        .status-pending {
            color: #e67e22;
            font-weight: bold;
        }
        .status-completed {
            color: #27ae60;
            font-weight: bold;
        }
        .border {
            border-top: 2px solid #005566;
            margin-top: 1px;
        }
    </style>
</head>
<body>
    <div class="invoice-print">
        <div class="header">
            <div class="company-info">
                @if (file_exists($data['logo_path']))
                    <img src="{{ $data['logo_path'] }}" alt="Paradis-Deco Logo">
                @endif
                <p><strong>Paradis-Deco</strong></p>
                <p>Msaken, Sousse, Tunisie</p>
                <p>Téléphone: +216 ** *** ***</p>
                <p>Email: Paradis-deco@gmail.com</p>
            </div>
            <div class="invoice-info">
                <h1>Facture #{{ $data['numero_commande'] }}</h1>
                <p><strong>{{ $data['client'] }}</strong></p>
                <p><strong>{{ $data['phone'] }}</strong></p>
                <p>{{ $data['adresse'] }}</p>
                <p><strong>Date:</strong> {{ $data['date'] }}</p>
                <p><strong>Statut:</strong>
                    <span class="{{ str_contains(strtolower($data['statut']), 'en attente') ? 'status-pending' : 'status-completed' }}">
                        {{ $data['statut'] }}
                    </span>
                </p>
                @if ($data['shipped_at'] !== 'N/A')
                    <p><strong>Date d'expédition:</strong> {{ $data['shipped_at'] }}</p>
                @endif
                @if ($data['paid_at'] !== 'N/A')
                    <p><strong>Date de paiement:</strong> {{ $data['paid_at'] }}</p>
                @endif
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th>Prix Unitaire</th>
                    <th>Qté</th>
                    <th>TVA (%)</th>
                    <th>Total HT</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['items'] as $item)
                    @php
                        $ligneHT = $item->unit_price * $item->quantity;
                    @endphp
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ number_format($item->unit_price, 2, ',', ' ') }} {{ $data['currency'] }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $data['tax_rate'] }} %</td>
                        <td>{{ number_format($ligneHT, 2, ',', ' ') }} {{ $data['currency'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center">Aucun produit disponible</td>
                    </tr>
                @endforelse
                <tr class="total-row">
                    <td colspan="4">Sous-total HT</td>
                    <td>{{ number_format($data['subtotal_ht'], 2, ',', ' ') }} {{ $data['currency'] }}</td>
                </tr>
                @if ($data['shipping_cost'] > 0)
                    <tr class="total-row">
                        <td colspan="4">Frais de livraison</td>
                        <td>{{ number_format($data['shipping_cost'], 2, ',', ' ') }} {{ $data['currency'] }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td colspan="4">TVA ({{ $data['tax_rate'] }} %)</td>
                    <td>{{ number_format($data['tax_tva'], 2, ',', ' ') }} {{ $data['currency'] }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="4">Total TTC</td>
                    <td>{{ number_format($data['total_ttc'], 2, ',', ' ') }} {{ $data['currency'] }}</td>
                </tr>
            </tbody>
        </table>
        <div class="terms-conditions">
            <h3>Termes & Conditions</h3>
            <ol style="font-size:11px; margin-left:18px; line-height:1.4;">
                <li>Facture payable comptant à réception ; tout retard entraîne des intérêts au TMM + 3 pts et une indemnité forfaitaire de 40 {{ $data['currency'] }}.</li>
                <li>Les marchandises restent propriété du vendeur jusqu’au paiement intégral.</li>
                <li>Toute réclamation doit être formulée par écrit dans les 8 jours suivant la réception.</li>
                <li>En cas de litige, seuls les tribunaux du siège du vendeur sont compétents.</li>
            </ol>
        </div>
        <div class="footer">
            <p>Généré par Paradis-Deco</p>
        </div>
    </div>
</body>
</html>
