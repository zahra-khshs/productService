<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $cacheKey = 'products:all';
        $products = Redis::get($cacheKey);

        if (!$products) {
            $products = Product::paginate(10);
            Redis::setex($cacheKey, 3600, json_encode($products));
        } else {
            $products = json_decode($products, true);
        }

        return response()->json($products);
    }

    public function show($id): JsonResponse
    {
        $product = Redis::get("product:{$id}");

        if (!$product) {
            $product = Product::findOrFail($id);
            Redis::set("product:{$id}", json_encode($product));
        } else {
            $product = json_decode($product, true);
        }

        return response()->json($product);
    }

    public function byCategory($category): JsonResponse
    {
        $cacheKey = "products:category:{$category}";
        $products = Redis::get($cacheKey);

        if (!$products) {
            $products = Product::where('category', $category)->paginate(10);
            Redis::setex($cacheKey, 3600, json_encode($products));
        } else {
            $products = json_decode($products, true);
        }

        return response()->json($products);
    }
}
