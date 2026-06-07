<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('creator')->latest();

        if (! $request->user()?->isAdmin()) $query->active();
        if ($request->boolean('featured'))  $query->featured();
        if ($request->filled('category'))   $query->where('category', $request->category);

        return $this->paginated(
            ProductResource::collection($query->paginate($request->integer('per_page', 12)))
        );
    }

    public function show(Product $product): JsonResponse
    {
        return $this->success(new ProductResource($product->load('creator')));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'stock_quantity' => 'integer|min:0',
            'image_url'      => 'nullable|url',
            'images'         => 'nullable|array',
            'category'       => 'nullable|string|max:100',
            'is_active'      => 'boolean',
            'is_featured'    => 'boolean',
        ]);

        $data['slug']       = Str::slug($data['name']) . '-' . Str::random(5);
        $data['created_by'] = auth()->id();

        return $this->success(new ProductResource(Product::create($data)), 'Created', 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'name'           => 'sometimes|string|max:255',
            'description'    => 'sometimes|nullable|string',
            'price'          => 'sometimes|numeric|min:0',
            'stock_quantity' => 'sometimes|integer|min:0',
            'image_url'      => 'sometimes|nullable|url',
            'images'         => 'sometimes|nullable|array',
            'category'       => 'sometimes|nullable|string|max:100',
            'is_active'      => 'sometimes|boolean',
            'is_featured'    => 'sometimes|boolean',
        ]);

        $product->update($data);
        return $this->success(new ProductResource($product->load('creator')), 'Updated');
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return $this->success(null, 'Deleted');
    }
}
