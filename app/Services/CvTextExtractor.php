<?php

namespace App\Services;

use Spatie\PdfToText\Pdf;
use Illuminate\Support\Facades\Http;

class CvTextExtractor
{
    public static function extract(string $cvFile): string
    {
        $path = storage_path("app/tmp/{$cvFile}");
        @mkdir(dirname($path), 0777, true);

        /**
         * 🧩 Construcción robusta de la URL (con fallback)
         * Evita el error "Could not resolve host: storage"
         */
        $baseUrl = rtrim(env('SUPABASE_URL', 'https://zescvmlacfujcgmjcskq.supabase.co'), '/');
        $bucket  = env('SUPABASE_BUCKET', 'cvs');
        $url     = "{$baseUrl}/storage/v1/object/{$bucket}/" . ltrim($cvFile, '/');

        // 📥 Descargar desde Supabase
        $response = Http::withHeaders([
            'apikey'        => env('SUPABASE_SERVICE_ROLE'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE'),
        ])->get($url);

        if ($response->failed()) {
            \Log::error("❌ No se pudo descargar el CV desde Supabase", [
                'url'    => $url,
                'cvFile' => $cvFile,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return '';
        }

        file_put_contents($path, $response->body());

        // ⚡ Ruta al binario pdftotext (desde .env, con fallback a "pdftotext")
        $pdftotextPath = config('pdf.pdftotext_path', '/usr/bin/pdftotext');

        if (str_ends_with(strtolower($cvFile), '.pdf')) {
            try {
                $text = Pdf::getText($path, $pdftotextPath);

                // 🛠️ Forzar conversión desde ISO-8859-1 → UTF-8
                return mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
            } catch (\Throwable $e) {
                \Log::error('Error extrayendo texto de PDF', [
                    'file'    => $cvFile,
                    'message' => $e->getMessage(),
                ]);
                return '';
            }
        }

        return '';
    }
}

