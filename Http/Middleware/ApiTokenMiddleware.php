<?php

namespace Modules\KnowledgeBaseApiModule\Http\Middleware;

use App\Option;
use Closure;
use Illuminate\Http\Request;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $stored_token = Option::get('knowledgebase_api_token');
        
        // If no token is set in the system, deny all access
        if (!$stored_token) {
            return response()->json(['error' => 'API token not configured'], 403);
        }
        
        // Check for token in query parameters
        $token = $request->query('token');
        
        if (!$token || $token !== $stored_token) {
            return response()->json(['error' => 'Invalid or missing API token'], 401);
        }
        
        return $next($request);
    }
} 