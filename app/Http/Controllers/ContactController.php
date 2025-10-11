<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;
use App\Models\Config;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;
class ContactController extends Controller
{
   public function index()
    {
        $config = Configuration::first();

        return view('front-office.contact.index', [
            'config' => $config
        ]);
    }

    public function submit(Request $request)
    {
        // Validation des données du formulaire
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|min:10|max:2000'
        ]);

        // Envoi de l'email (vous devez configurer votre mailer dans .env)
        Mail::to(config('mail.contact_to'))
            ->send(new ContactFormMail($validated));

        // Redirection avec message de succès
        return redirect()->route('contact')
            ->with('success', 'Votre message a été envoyé avec succès ! Nous vous répondrons dès que possible.');
    }
}
