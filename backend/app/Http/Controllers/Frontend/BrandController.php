<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Admin\Brands;
use App\Models\Admin\Products;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Get all brands with product counts
     */
    public function index()
    {
        try {
            $brands = Brands::withCount(['products' => function($query) {
                $query->where('status', true);
            }])->get();

            $formattedBrands = $brands->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'title' => $brand->title,
                    'slug' => $brand->slug,
                    'logo_url' => $brand->logo ? asset('storage/' . $brand->logo) : null,
                    'product_count' => $brand->products_count,
                ];
            });

            return response()->json([
                'status' => true,
                'data' => $formattedBrands
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch brands',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products by brand slug
     */
    public function products($slug, Request $request)
    {
        try {
            $brand = Brands::where('slug', $slug)->firstOrFail();

            $query = Products::with(['category', 'brands'])
                ->where('status', true)
                ->where('brand_id', $brand->id);

            // Apply filters
            if ($request->has('min_price') && $request->min_price) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->has('max_price') && $request->max_price) {
                $query->where('price', '<=', $request->max_price);
            }

            if ($request->has('category') && $request->category) {
                $query->where('category_id', $request->category);
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
                        $query->orderBy('created_at', 'desc');
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
                    'category_title' => $product->category->title ?? '',
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
                'brand' => [
                    'id' => $brand->id,
                    'title' => $brand->title,
                    'slug' => $brand->slug,
                    'logo_url' => $brand->logo ? asset('storage/' . $brand->logo) : null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch brand products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured/popular brands
     */
    public function featured()
    {
        try {
            // Get brands with the most active products
            $brands = Brands::withCount(['products' => function($query) {
                $query->where('status', true);
            }])
            ->orderBy('products_count', 'desc')
            ->limit(8) // Limit to 8 featured brands
            ->get();

            $formattedBrands = $brands->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'title' => $brand->title,
                    'slug' => $brand->slug,
                    'logo_url' => $brand->logo ? asset('storage/' . $brand->logo) : null,
                    'product_count' => $brand->products_count,
                ];
            });

            return response()->json([
                'status' => true,
                'data' => $formattedBrands
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch featured brands',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
