<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Admin\ProductCategories as Category;
use App\Models\Admin\Products;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all categories
     */
public function index()
{
    try {
        // Use withCount on the direct relationship
        $categories = Category::withCount(['products' => function($query) {
            $query->where('status', true);
        }])->get();

        $formattedCategories = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'title' => $category->title,
                'slug' => $category->slug,
                'product_count' => $category->products_count,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $formattedCategories
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Failed to fetch categories',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Get products by category slug
     */
    public function products($slug, Request $request)
    {
        try {
            $category = Category::where('slug', $slug)->firstOrFail();

            $query = Products::with(['category', 'brands'])
                ->where('status', true)
                ->where('category_id', $category->id);

            // Apply filters
            if ($request->has('min_price') && $request->min_price) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->has('max_price') && $request->max_price) {
                $query->where('price', '<=', $request->max_price);
            }

            if ($request->has('brand') && $request->brand) {
                $query->where('brand_id', $request->brand);
            }

            // Sorting
            if ($request->has('sort_by')) {
                switch ($request->sort_by) {
                    case 'price_low':
                        $query->orderBy('price', 'asc');
                        break;
                    case 'price_high':
                        $query->orderBy('price', 'desc');
                        break;
                    case 'name_asc':
                        $query->orderBy('name', 'asc');
                        break;
                    case 'name_desc':
                        $query->orderBy('name', 'desc');
                        break;
                    case 'newest':
                    default:
                        $query->orderBy('created_at', 'desc'); // Fixed: changed 'created' to 'created_at'
                        break;
                }
            } else {
                $query->orderBy('created_at', 'desc'); // Default sorting
            }

            $perPage = $request->get('per_page', 12);
            $products = $query->paginate($perPage);

            $formattedProducts = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->price,
                    'main_image_url' => $product->main_image_name
                        ? asset('storage/' . $product->main_image_name)
                        : asset('images/default-product.jpg'),
                    'brand_title' => $product->brands->title ?? '',
                ];
            });

            return response()->json([
                'status' => true,
                'data' => $formattedProducts,
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ],
                'category' => [
                    'id' => $category->id,
                    'title' => $category->title,
                    'slug' => $category->slug,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch category products',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
