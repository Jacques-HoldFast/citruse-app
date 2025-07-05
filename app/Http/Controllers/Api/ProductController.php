<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products with current pricing
     * All authenticated users can view products
     */
    public function index(Request $request)
    {
        $query = Product::withCurrentPricing();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_code', 'ILIKE', "%{$search}%")
                    ->orWhere('description', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by product code prefix (e.g., 'FR-' for Citrusdal products)
        if ($request->has('code_prefix')) {
            $query->where('product_code', 'LIKE', $request->code_prefix . '%');
        }

        $products = $query->orderBy('product_code')->paginate(20);

        return response()->json($products);
    }

    /**
     * Display the specified product with all pricing history
     */
    public function show(Product $product)
    {
        $product->load([
            'prices' => function ($query) {
                $query->orderBy('year', 'desc');
            }
        ]);

        return response()->json([
            'product' => $product,
            'current_price' => $product->getCurrentPricePerKg(),
            'product_info' => $product->product_info,
        ]);
    }

    /**
     * Get product pricing for a specific year
     */
    public function pricing(Request $request)
    {
        $year = $request->input('year', date('Y'));

        $products = Product::with([
            'prices' => function ($query) use ($year) {
                $query->where('year', $year);
            }
        ])->orderBy('product_code')->get();

        return response()->json([
            'year' => $year,
            'products' => $products->map(function ($product) use ($year) {
                $price = $product->prices->first();
                return [
                    'id' => $product->id,
                    'product_code' => $product->product_code,
                    'description' => $product->description,
                    'price_per_kg' => $price ? $price->price_per_kg : null,
                    'formatted_price' => $price ? $price->formatted_price : 'No pricing',
                ];
            })
        ]);
    }

    /**
     * Get available pricing years
     */
    public function pricingYears()
    {
        $years = ProductPrice::getAvailableYears();

        return response()->json([
            'years' => $years,
            'current_year' => date('Y')
        ]);
    }

    /**
     * Store a newly created product
     * Only System Admins can create products
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_code' => 'required|string|max:20|unique:products,product_code',
            'description' => 'required|string|max:255',
            'prices' => 'required|array|min:1',
            'prices.*.year' => 'required|integer|min:2020|max:2030',
            'prices.*.price_per_kg' => 'required|numeric|min:0|max:9999.99',
        ]);

        $product = Product::create([
            'product_code' => $validated['product_code'],
            'description' => $validated['description'],
        ]);

        // Create pricing for each year
        foreach ($validated['prices'] as $priceData) {
            ProductPrice::create([
                'product_id' => $product->id,
                'year' => $priceData['year'],
                'price_per_kg' => $priceData['price_per_kg'],
            ]);
        }

        $product->load('prices');

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }

    /**
     * Update the specified product
     * Only System Admins can update products
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_code' => 'sometimes|required|string|max:20|unique:products,product_code,' . $product->id,
            'description' => 'sometimes|required|string|max:255',
        ]);

        $product->update($validated);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }

    /**
     * Update product pricing for a specific year
     * Only System Admins can update pricing
     */
    public function updatePricing(Request $request, Product $product)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'price_per_kg' => 'required|numeric|min:0|max:9999.99',
        ]);

        ProductPrice::updateOrCreate(
            [
                'product_id' => $product->id,
                'year' => $validated['year']
            ],
            ['price_per_kg' => $validated['price_per_kg']]
        );

        return response()->json([
            'message' => 'Product pricing updated successfully',
            'product' => $product->load('prices')
        ]);
    }

    /**
     * Remove the specified product
     * Only System Admins can delete products
     */
    public function destroy(Product $product)
    {
        // Check if product is used in any purchase orders
        if ($product->purchaseOrderItems()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete product that is used in purchase orders'
            ], 422);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    /**
     * Get product catalog with categories
     * Public method for catalog display
     */
    public function catalog()
    {
        $products = Product::withCurrentPricing()
            ->orderBy('product_code')
            ->get()
            ->groupBy(function ($product) {
                // Group by prefix (FR- or GT)
                return substr($product->product_code, 0, 2);
            });

        return response()->json([
            'catalog' => $products->map(function ($group, $prefix) {
                $categoryName = match ($prefix) {
                    'FR' => 'Citrusdal Products',
                    'GT' => 'Grahamstown Products',
                    default => 'Other Products'
                };

                return [
                    'category' => $categoryName,
                    'prefix' => $prefix,
                    'products' => $group->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'product_code' => $product->product_code,
                            'description' => $product->description,
                            'current_price' => $product->getCurrentPricePerKg(),
                            'formatted_price' => $product->getCurrentPrice() ?
                                $product->getCurrentPrice()->formatted_price :
                                'No pricing'
                        ];
                    })
                ];
            })->values()
        ]);
    }
}