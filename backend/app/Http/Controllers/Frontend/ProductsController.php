<?php

/**
 * Products Class
 *
 * @package    ProductsController
 * @version    Release: 1.0.0
 * @since      Class available since Release 1.0.0
 */

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\Settings;
use App\Models\Admin\Permissions;
use App\Models\Admin\Products;
use App\Models\Admin\ProductCategories;
use App\Models\Admin\ProductCategoryRelation;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Admin\AppController;
use App\Http\Controllers\ApiController;
use App\Models\Admin\BrandProducts;
use App\Models\Admin\Brands;
use App\Models\Admin\Colours;
use App\Models\Admin\ProductSizeRelation;
use App\Models\Admin\ProductSubCategories;
use App\Models\Admin\ProductVariant;
use App\Models\Admin\ProductVariantImage;
use App\Models\Admin\Sizes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductsController extends ApiController
{

    public function index(Request $request)
    {
        try {
            $query = Products::with([
                'category',
                'brands',
                'variants' => function ($query) {
                    $query->with(['sizes' => function ($q) {
                        $q->withPivot(['stock', 'price']);
                    }]);
                }
            ])
                ->where('status', true)
                ->orderBy('created', 'desc');

            // Apply filters
            if ($request->has('category') && $request->category) {
                $query->where('category_id', $request->category);
            }

            if ($request->has('brand') && $request->brand) {
                $query->where('brands_id', $request->brand);
            }

            if ($request->has('min_price') && $request->min_price) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->has('max_price') && $request->max_price) {
                $query->where('price', '<=', $request->max_price);
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            }

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
                        $query->orderBy('created', 'desc');
                        break;
                }
            }

            // Get paginated results
            $perPage = $request->get('per_page', 12);
            $products = $query->paginate($perPage);

            // Format response
            $formattedProducts = $products->map(function ($product) {
                $totalStock = $product->stock;
                $hasStock = $totalStock > 0;

                // Calculate price range and prepare variant data
                $minPrice = $product->price;
                $maxPrice = $product->price;
                $variantsData = [];

                if ($product->variants->isNotEmpty()) {
                    $variantPrices = [];
                    $anyVariantHasStock = false; // Track if ANY variant has stock

                    foreach ($product->variants as $variant) {
                        $variantStock = 0;
                        $availableSizes = [];

                        foreach ($variant->sizes as $size) {
                            $sizeStock = $size->pivot->stock ?? 0;
                            $variantStock += $sizeStock;
                            $totalStock += $sizeStock;

                            if ($sizeStock > 0) {
                                $anyVariantHasStock = true; // Mark that we found stock
                                $availableSizes[] = [
                                    'id' => $size->id,
                                    'size_title' => $size->size_title,
                                    'price' => $size->pivot->price ?? $product->price,
                                    'stock' => $sizeStock,
                                    'has_stock' => $sizeStock > 0,
                                ];
                            }

                            $variantPrices[] = $size->pivot->price ?? $product->price;
                        }

                        $variantsData[] = [
                            'id' => $variant->id,
                            'color' => $variant->color,
                            'color_name' => $variant->color_name,
                            'has_stock' => $variantStock > 0,
                            'sizes' => $availableSizes,
                        ];
                    }

                    if (!empty($variantPrices)) {
                        $minPrice = min($variantPrices);
                        $maxPrice = max($variantPrices);
                    }

                    // IMPORTANT: If product has variants, has_stock should be based on variant stock only
                    $hasStock = $anyVariantHasStock;
                }

                // If min_price is 0.00, set it to product price
                if ($minPrice == 0.00 || $minPrice == '0.00') {
                    $minPrice = $product->price;
                }
                if ($maxPrice == 0.00 || $maxPrice == '0.00') {
                    $maxPrice = $product->price;
                }

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'description' => $product->description,
                    'price' => $product->price,
                    'purchase_price' => $product->purchase_price,
                    'sku' => $product->sku,
                    'stock' => $totalStock,
                    'has_stock' => $hasStock, // Correctly calculated
                    'gender' => $product->gender,
                    'status' => $product->status,
                    'category_id' => $product->category_id,
                    'category_title' => $product->category->title ?? '',
                    'brand_id' => $product->brands_id,
                    'brand_title' => $product->brands->title ?? '',
                    'main_image_url' => $product->main_image_name
                        ? asset('storage/' . $product->main_image_name)
                        : asset('images/default-product.jpg'),
                    'min_price' => $minPrice,
                    'max_price' => $maxPrice,
                    'variants' => $variantsData,
                    'created_at' => $product->created,
                    'updated_at' => $product->updated_at,
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
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured products with proper stock calculation
     */
    public function featured()
    {
        try {
            $products = Products::with([
                'category',
                'brands',
                'variants' => function ($query) {
                    $query->with(['sizes' => function ($q) {
                        $q->withPivot(['stock', 'price']);
                    }]);
                }
            ])
                ->where('status', true)
                ->limit(12)
                ->orderBy('created', 'desc')
                ->get();

            $formattedProducts = $products->map(function ($product) {
                $totalStock = $product->stock;
                $hasStock = $totalStock > 0;

                // Calculate price range and prepare variant data
                $minPrice = $product->price;
                $maxPrice = $product->price;
                $variantsData = [];

                if ($product->variants->isNotEmpty()) {
                    $variantPrices = [];
                    $anyVariantHasStock = false; // Track if ANY variant has stock

                    foreach ($product->variants as $variant) {
                        $variantStock = 0;
                        $availableSizes = [];

                        foreach ($variant->sizes as $size) {
                            $sizeStock = $size->pivot->stock ?? 0;
                            $variantStock += $sizeStock;
                            $totalStock += $sizeStock;

                            if ($sizeStock > 0) {
                                $anyVariantHasStock = true; // Mark that we found stock
                                $availableSizes[] = [
                                    'id' => $size->id,
                                    'size_title' => $size->size_title,
                                    'price' => $size->pivot->price ?? $product->price,
                                    'stock' => $sizeStock,
                                    'has_stock' => $sizeStock > 0,
                                ];
                            }

                            $variantPrices[] = $size->pivot->price ?? $product->price;
                        }

                        $variantsData[] = [
                            'id' => $variant->id,
                            'color' => $variant->color,
                            'color_name' => $variant->color_name,
                            'has_stock' => $variantStock > 0, // Correctly set variant has_stock
                            'sizes' => $availableSizes,
                        ];
                    }

                    if (!empty($variantPrices)) {
                        $minPrice = min($variantPrices);
                        $maxPrice = max($variantPrices);
                    }

                    // IMPORTANT: If product has variants, has_stock should be based on variant stock only
                    // This is the key fix - ignore main product stock when there are variants
                    $hasStock = $anyVariantHasStock;
                }

                // If min_price is 0.00, set it to product price
                if ($minPrice == 0.00 || $minPrice == '0.00') {
                    $minPrice = $product->price;
                }
                if ($maxPrice == 0.00 || $maxPrice == '0.00') {
                    $maxPrice = $product->price;
                }

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'description' => $product->description,
                    'price' => $product->price,
                    'sku' => $product->sku,
                    'stock' => $totalStock,
                    'has_stock' => $hasStock, // This will now be correct
                    'category_title' => $product->category->title ?? '',
                    'brand_title' => $product->brands->title ?? '',
                    'main_image_url' => $product->main_image_name
                        ? asset('storage/' . $product->main_image_name)
                        : asset('images/default-product.jpg'),
                    'min_price' => $minPrice,
                    'max_price' => $maxPrice,
                    'variants' => $variantsData,
                ];
            });

            return response()->json([
                'status' => true,
                'data' => $formattedProducts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch featured products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single product by slug with complete variant data
     */
    public function show($slug)
    {
        try {
            $product = Products::with([
                'category',
                'subcategories',
                'variants' => function ($query) {
                    $query->orderBy('color_name');
                },
                'variants.sizes' => function ($query) {
                    $query->withPivot(['price', 'stock'])->orderBy('size_title');
                },
                'variants.images',
                'brands'
            ])->where('slug', $slug)
                ->where('status', true)
                ->firstOrFail();

            // Calculate total available stock
            $totalStock = $product->stock;
            $hasStock = $totalStock > 0;

            // Check variant stock
            foreach ($product->variants as $variant) {
                foreach ($variant->sizes as $size) {
                    $variantStock = $size->pivot->stock ?? 0;
                    $totalStock += $variantStock;
                    if ($variantStock > 0) {
                        $hasStock = true;
                    }
                }
            }

            // Calculate price range from variants
            $minPrice = $product->price;
            $maxPrice = $product->price;

            if ($product->variants->isNotEmpty()) {
                $variantPrices = [];
                foreach ($product->variants as $variant) {
                    foreach ($variant->sizes as $size) {
                        $variantPrices[] = $size->pivot->price ?? $product->price;
                    }
                }

                if (!empty($variantPrices)) {
                    $minPrice = min($variantPrices);
                    $maxPrice = max($variantPrices);
                }
            }

            // Prepare detailed response
            $response = [
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'description' => $product->description,
                    'price' => $product->price,
                    'purchase_price' => $product->purchase_price,
                    'sku' => $product->sku,
                    'stock' => $totalStock,
                    'has_stock' => $hasStock,
                    'gender' => $product->gender,
                    'category_id' => $product->category_id,
                    'category_title' => $product->category->title ?? '',
                    'brand_id' => $product->brands_id,
                    'brand_title' => $product->brands->title ?? '',
                    'main_image_url' => $product->main_image_name
                        ? asset('storage/' . $product->main_image_name)
                        : asset('images/default-product.jpg'),
                    'size_guide_url' => $product->size_guide_name
                        ? asset('storage/' . $product->size_guide_name)
                        : null,
                    'min_price' => $minPrice,
                    'max_price' => $maxPrice,
                    'created_at' => $product->created,
                ],
                'subcategories' => $product->subcategories->map(function ($subcat) {
                    return [
                        'id' => $subcat->id,
                        'title' => $subcat->title,
                    ];
                }),
                'variants' => $product->variants->map(function ($variant) {
                    // Filter sizes that have stock > 0
                    $availableSizes = $variant->sizes->filter(function ($size) {
                        return ($size->pivot->stock ?? 0) > 0;
                    });

                    return [
                        'id' => $variant->id,
                        'color' => $variant->color,
                        'color_name' => $variant->color_name,
                        'has_stock' => $availableSizes->count() > 0,
                        'sizes' => $variant->sizes->map(function ($size) {
                            return [
                                'id' => $size->id,
                                'size_title' => $size->size_title,
                                'price' => $size->pivot->price ?? 0,
                                'stock' => $size->pivot->stock ?? 0,
                                'has_stock' => ($size->pivot->stock ?? 0) > 0,
                            ];
                        })->sortBy('size_title')->values(),
                        'images' => $variant->images->map(function ($image) {
                            return [
                                'id' => $image->id,
                                'url' => asset('storage/' . $image->image_name),
                            ];
                        }),
                    ];
                })->filter(function ($variant) {
                    return $variant['has_stock']; // Only include variants with stock
                })->values(),
                'related_products' => $this->getRelatedProducts($product->category_id, $product->id),
            ];

            return response()->json([
                'status' => true,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get product by ID
     */
    public function showById($id)
    {
        try {
            $product = Products::with(['category', 'brands', 'variants.sizes' => function ($q) {
                $q->withPivot(['stock', 'price']);
            }])
                ->where('id', $id)
                ->where('status', true)
                ->firstOrFail();

            // Calculate total stock
            $totalStock = $product->stock;
            foreach ($product->variants as $variant) {
                foreach ($variant->sizes as $size) {
                    $totalStock += $size->pivot->stock ?? 0;
                }
            }

            $response = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => $product->price,
                'sku' => $product->sku,
                'stock' => $totalStock,
                'has_stock' => $totalStock > 0,
                'category_title' => $product->category->title ?? '',
                'brand_title' => $product->brands->title ?? '',
                'main_image_url' => $product->main_image_name
                    ? asset('storage/' . $product->main_image_name)
                    : asset('images/default-product.jpg'),
            ];

            return response()->json([
                'status' => true,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Search products
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q');

            if (!$query) {
                return response()->json([
                    'status' => true,
                    'data' => []
                ]);
            }

            $products = Products::with(['category', 'brands', 'variants.sizes' => function ($q) {
                $q->withPivot(['stock', 'price']);
            }])
                ->where('status', true)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhere('sku', 'like', "%{$query}%");
                })
                ->limit(10)
                ->get();

            $formattedProducts = $products->map(function ($product) {
                // Calculate total stock
                $totalStock = $product->stock;
                foreach ($product->variants as $variant) {
                    foreach ($variant->sizes as $size) {
                        $totalStock += $size->pivot->stock ?? 0;
                    }
                }

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->price,
                    'stock' => $totalStock,
                    'has_stock' => $totalStock > 0,
                    'main_image_url' => $product->main_image_name
                        ? asset('storage/' . $product->main_image_name)
                        : asset('images/default-product.jpg'),
                    'category_title' => $product->category->title ?? '',
                    'brand_title' => $product->brands->title ?? '',
                ];
            });

            return response()->json([
                'status' => true,
                'data' => $formattedProducts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get related products
     */
    public function related($id, Request $request)
    {
        try {
            $currentProduct = Products::findOrFail($id);
            $categoryId = $request->get('category', $currentProduct->category_id);

            $relatedProducts = Products::with(['category', 'brands', 'variants.sizes' => function ($q) {
                $q->withPivot(['stock', 'price']);
            }])
                ->where('status', true)
                ->where('category_id', $categoryId)
                ->where('id', '!=', $id)
                ->limit(4)
                ->inRandomOrder()
                ->get();

            $formattedProducts = $relatedProducts->map(function ($product) {
                // Calculate total stock
                $totalStock = $product->stock;
                foreach ($product->variants as $variant) {
                    foreach ($variant->sizes as $size) {
                        $totalStock += $size->pivot->stock ?? 0;
                    }
                }

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->price,
                    'stock' => $totalStock,
                    'has_stock' => $totalStock > 0,
                    'main_image_url' => $product->main_image_name
                        ? asset('storage/' . $product->main_image_name)
                        : asset('images/default-product.jpg'),
                    'category_title' => $product->category->title ?? '',
                    'brand_title' => $product->brands->title ?? '',
                ];
            });

            return response()->json([
                'status' => true,
                'data' => $formattedProducts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch related products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper function to get related products
     */
private function getRelatedProducts($categoryId, $excludeId, $limit = 4)
{
    return Products::with([
        'category',
        'brands',
        'variants' => function($query) {
            $query->with(['sizes' => function($q) {
                $q->withPivot(['stock', 'price']);
            }]);
        }
    ])
        ->where('status', true)
        ->where('category_id', $categoryId)
        ->where('id', '!=', $excludeId)
        ->limit($limit)
        ->inRandomOrder()
        ->get()
        ->map(function ($product) {
            $totalStock = $product->stock;
            $hasStock = $totalStock > 0;

            // Calculate price range and variant data
            $minPrice = $product->price;
            $maxPrice = $product->price;

            if ($product->variants->isNotEmpty()) {
                $variantPrices = [];
                $anyVariantHasStock = false; // Track if ANY variant has stock

                foreach ($product->variants as $variant) {
                    $variantStock = 0;

                    foreach ($variant->sizes as $size) {
                        $sizeStock = $size->pivot->stock ?? 0;
                        $variantStock += $sizeStock;
                        $totalStock += $sizeStock;

                        if ($sizeStock > 0) {
                            $anyVariantHasStock = true;
                        }

                        $variantPrices[] = $size->pivot->price ?? $product->price;
                    }
                }

                if (!empty($variantPrices)) {
                    $minPrice = min($variantPrices);
                    $maxPrice = max($variantPrices);
                }

                // If product has variants, has_stock should be based on variant stock only
                $hasStock = $anyVariantHasStock;
            }

            // If min_price is 0.00, set it to product price
            if ($minPrice == 0.00 || $minPrice == '0.00') {
                $minPrice = $product->price;
            }
            if ($maxPrice == 0.00 || $maxPrice == '0.00') {
                $maxPrice = $product->price;
            }

            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'stock' => $totalStock,
                'has_stock' => $hasStock, // Correctly calculated
                'main_image_url' => $product->main_image_name
                    ? asset('storage/' . $product->main_image_name)
                    : asset('images/default-product.jpg'),
                'category_title' => $product->category->title ?? '',
                'brand_title' => $product->brands->title ?? '',
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                // Include variant data if needed by frontend
                'variants' => $product->variants->map(function ($variant) {
                    $variantStock = 0;
                    $availableSizes = [];

                    foreach ($variant->sizes as $size) {
                        $sizeStock = $size->pivot->stock ?? 0;
                        $variantStock += $sizeStock;

                        if ($sizeStock > 0) {
                            $availableSizes[] = [
                                'id' => $size->id,
                                'size_title' => $size->size_title,
                                'price' => $size->pivot->price ?? 0,
                                'stock' => $sizeStock,
                                'has_stock' => $sizeStock > 0,
                            ];
                        }
                    }

                    return [
                        'id' => $variant->id,
                        'color' => $variant->color,
                        'color_name' => $variant->color_name,
                        'has_stock' => $variantStock > 0,
                        'sizes' => $availableSizes,
                    ];
                })->toArray(),
            ];
        });
}

    /**
     * Get categories
     */
    public function categories()
    {
        try {
            $categories = ProductCategories::where('status', true)
                ->withCount(['products' => function ($query) {
                    $query->where('status', true);
                }])
                ->orderBy('title')
                ->get();

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
     * Get brands
     */
    public function brands()
    {
        try {
            $brands = Brands::where('status', true)
                ->withCount(['products' => function ($query) {
                    $query->where('status', true);
                }])
                ->orderBy('title')
                ->get();

            $formattedBrands = $brands->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'title' => $brand->title,
                    'slug' => $brand->slug,
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
}
