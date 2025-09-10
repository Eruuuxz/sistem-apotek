<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        // Untuk request JSON, kembalikan JSON response
        if ($request->expectsJson()) {
            return parent::render($request, $e);
        }

        // Handle HTTP exceptions (404, 500, etc.)
        if ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();
            
            // Cek apakah view error kustom tersedia
            if (view()->exists("errors.{$status}")) {
                return response()->view("errors.{$status}", ['exception' => $e], $status);
            }
        }

        // Untuk production, log error dan tampilkan halaman error kustom
        if (app()->environment('production')) {
            \Log::error('Unhandled Exception', [
                'exception' => $e,
                'url' => $request->url(),
                'method' => $request->method(),
                'user_id' => auth()->id(),
            ]);
            
            return response()->view('errors.500', ['exception' => $e], 500);
        }

        return parent::render($request, $e);
    }
}
