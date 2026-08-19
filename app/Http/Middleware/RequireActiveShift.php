<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireActiveShift
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->isAdmin()) {
            $activeShift = auth()->user()->activeShift();
            if (!$activeShift) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Anda harus membuka shift terlebih dahulu.'], 403);
                }
                return redirect()->route('pos.shifts.index')->with('error', 'Anda harus membuka shift terlebih dahulu sebelum melakukan transaksi.');
            }
        }
        
        return $next($request);
    }
}
