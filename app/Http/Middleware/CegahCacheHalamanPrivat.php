<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Melarang browser/proxy menyimpan halaman yang hanya boleh dilihat owner.
 * Tanpa ini, tombol "Back" setelah keluar bisa menampilkan salinan halaman lama.
 */
class CegahCacheHalamanPrivat
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-store, private, max-age=0');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }
}
