<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau message de {{ $data['name'] }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5; color: #333333; max-width: 600px; margin: 0 auto; padding: 25px 20px; background-color: #f5f5f5;">
    <!-- En-tête -->
    <div style="background: white; padding: 25px; border-radius: 8px 8px 0 0; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <div style="text-align: center; margin-bottom: 15px;">
            <h2 style="color: #2a4365; font-size: 22px; margin: 10px 0 5px;">Nouveau message de contact</h2>
            <p style="color: #4a5568; font-size: 15px; margin: 5px 0;">{{ now()->format('d/m/Y à H:i') }}</p>
        </div>
    </div>

    <!-- Coordonnées -->
    <div style="background: white; padding: 20px 25px; margin: 5px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <h3 style="color: #2a4365; font-size: 18px; margin: 0 0 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Coordonnées</h3>

        <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="margin-bottom: 8px;"><strong>Nom :</strong> {{ $data['name'] }}</li>
            <li style="margin-bottom: 8px;"><strong>Email :</strong> <a href="mailto:{{ $data['email'] }}" style="color: #2b6cb0;">{{ $data['email'] }}</a></li>
            @if (!empty($data['phone']))
                <li style="margin-bottom: 8px;"><strong>Téléphone :</strong> {{ $data['phone'] }}</li>
            @endif
        </ul>
    </div>

    <!-- Message -->
    <div style="background: white; padding: 20px 25px; margin: 5px 0; border-radius: 0 0 8px 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <h3 style="color: #2a4365; font-size: 18px; margin: 0 0 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Message</h3>
        <p style="margin: 0; white-space: pre-line;">{{ $data['message'] }}</p>
    </div>

    <p style="color: #718096; font-size: 13px; text-align: center; margin: 20px 0 0;">
        Répondez directement à cet email pour contacter {{ $data['name'] }}.
    </p>
</body>
</html>
