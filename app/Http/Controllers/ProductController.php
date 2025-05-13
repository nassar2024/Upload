<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->query('user_id');
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 10);

        $cacheKey = "products:user_{$userId}:page_{$page}:per_{$perPage}";
        $products = Cache::remember($cacheKey, 3600, function () use ($userId, $page, $perPage) {
            return Product::where('user_id', $userId)
                ->paginate($perPage, ['*'], 'page', $page);
        });

        return response()->json([
            'data' => $products->items(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'total' => $products->total(),
            'per_page' => $products->perPage(),
        ]);
    }

    public function clearCache(Request $request)
    {
        $userId = $request->user()->id;
        // Clear all cache entries for this user
        $pattern = "products:user_{$userId}:*";
        Cache::flush(); // Clears all cache; use with caution
        return response()->json(['message' => 'Product cache cleared for user ' . $userId]);
    }
}