<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Product;
use App\Http\Resources\OrderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['user', 'items'])->latest();

        // Non-admins see only their own orders
        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        } elseif ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $this->paginated(
            OrderResource::collection($query->paginate($request->integer('per_page', 15)))
        );
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        if (! $request->user()->isAdmin() && $order->user_id !== $request->user()->id) {
            return $this->error('Forbidden', 403);
        }

        return $this->success(new OrderResource($order->load(['user', 'items.product'])));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|uuid|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'shipping_address'   => 'nullable|array',
            'notes'              => 'nullable|string',
        ]);

        return DB::transaction(function () use ($data, $request) {
            $subtotal = 0;
            $orderItems = [];

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $lineTotal = $product->price * $item['quantity'];
                $subtotal += $lineTotal;

                $orderItems[] = [
                    'id'           => (string) Str::uuid(),
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'unit_price'   => $product->price,
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $lineTotal,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }

            $order = Order::create([
                'user_id'          => $request->user()->id,
                'order_number'     => 'ORD-' . strtoupper(Str::random(10)),
                'subtotal'         => $subtotal,
                'total'            => $subtotal,
                'shipping_address' => $data['shipping_address'] ?? null,
                'notes'            => $data['notes'] ?? null,
                'status'           => 'pending',
            ]);

            $order->items()->insert(array_map(fn($i) => array_merge($i, ['order_id' => $order->id]), $orderItems));

            return $this->success(new OrderResource($order->load('items')), 'Order created', 201);
        });
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $data = $request->validate([
            'status'              => 'required|in:pending,paid,processing,shipped,delivered,cancelled,refunded',
            'razorpay_order_id'   => 'nullable|string',
            'razorpay_payment_id' => 'nullable|string',
        ]);

        if (isset($data['status']) && $data['status'] === 'paid') {
            $data['paid_at'] = now();
        }

        $order->update($data);
        return $this->success(new OrderResource($order->load(['user', 'items'])), 'Status updated');
    }
}
