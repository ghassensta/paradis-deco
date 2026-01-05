<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ImageUploader
{
    /**
     * Upload une image en WebP dans le dossier public choisi.
     *
     * @param UploadedFile $image      L'image envoyée depuis le formulaire
     * @param string $folder           Le dossier de destination (ex: 'categories-produit')
     * @param int $quality             Qualité de compression WebP (0-100)
     * @return string|null             Le chemin du fichier sauvegardé ou null si erreur
     */
    public static function uploadWebp(UploadedFile $image, string $folder = 'uploads', int $quality = 80): ?string
    {
        try {
            $filename = uniqid() . '_' . time() . '.webp';
            $path = "{$folder}/{$filename}";

            $img = Image::read($image);
            $encoded = $img->toWebp($quality);

            Storage::disk('public')->put($path, (string) $encoded);

            return $path;
        } catch (\Throwable $th) {
            \Log::error("Erreur upload WebP : " . $th->getMessage());
            return null;
        }
    }

     public static function replace(?string $oldPath, UploadedFile $file, string $folder = 'uploads', int $quality = 80): ?string
    {
        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return self::uploadWebp($file, $folder, $quality);
    }


}
