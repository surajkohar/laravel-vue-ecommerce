<?php

/**
 * Products Class
 *
 * @package    ProductsController


 * @version    Release: 1.0.0
 * @since      Class available since Release 1.0.0
 */


namespace App\Http\Controllers\Admin\Products;

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
            $where = [];

            if ($request->get('search')) {
                $search = '%' . $request->get('search') . '%';
                $where[] = [
                    '(products.id LIKE ? OR products.name LIKE ? OR products.sku LIKE ? OR b.title LIKE ?)',
                    [$search, $search, $search, $search]
                ];
            }

            if ($createdFrom = $request->get('createdFrom')) {
                $where[] = ['products.created >= ?', [date('Y-m-d', strtotime($createdFrom))]];
            }

            if ($createdTo = $request->get('createdTo')) {
                $where[] = ['products.created <= ?', [date('Y-m-d 23:59:59', strtotime($createdTo))]];
            }

            Log::info($request->all());

            $categoryIds = $request->get('category');
            if (!empty($categoryIds)) {
                $categoryIds = is_string($categoryIds) ? explode(',', $categoryIds) : $categoryIds;
                $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
                $where[] = ["pc.id IN ($placeholders)", $categoryIds];
            }

            $brandIds = $request->get('brand');
            if (!empty($brandIds)) {
                $brandIds = is_string($brandIds) ? explode(',', $brandIds) : $brandIds;
                $placeholders = implode(',', array_fill(0, count($brandIds), '?'));
                $where[] = ["b.id IN ($placeholders)", $brandIds];
            }

            if ($request->get('status')) {
                $status = $request->get('status') === 'active' ? 1 : 0;
                $where[] = ['products.status = ?', [$status]];
            }

            $listing = Products::getListing($request, $where);

            return response()->json([
                'status' => true,
                'page' => $listing
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function filters() {}

    public function view($id)
    {
        try {
            $product = Products::with([
                'category',
                'subcategories',
                'variants',
                'variants.sizes',
                'variants.images',
                'brands'
            ])->findOrFail($id);

            // Prepare response data
            $response = [
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'status' => $product->status,
                    'price' => $product->price,
                    'purchase_price' => $product->purchase_price,
                    'sku' => $product->sku,
                    'stock' => $product->stock,
                    'gender' => $product->gender,
                    'category_id' => $product->category_id,
                    'category_title' => $product->category->title ?? '',
                    'brand_name' => $product->brands->title ?? '',
                    'main_image_url' => $product->main_image_name ? asset('storage/' . $product->main_image_name) : null,
                    'size_guide_url' => $product->size_guide_name ? asset('storage/' . $product->size_guide_name) : null,
                ],
                'subcategories' => $product->subcategories->map(function ($subcat) {
                    return [
                        'id' => $subcat->id,
                        'title' => $subcat->title,
                    ];
                }),
                'variants' => $product->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'color' => $variant->color,
                        'color_name' => $variant->color_name,
                        'sizes' => $variant->sizes->map(function ($size) {
                            return [
                                'id' => $size->id,
                                'size_title' => $size->size_title,
                                'price' => $size->pivot->price

                            ];
                        }),
                        'images' => $variant->images->map(function ($image) {
                            return [
                                'id' => $image->id,
                                'url' => asset('storage/' . $image->image_name),
                            ];
                        }),
                    ];
                }),
            ];

            return response()->json([
                'status' => true,
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            try {
                $data = Arr::undot($request->all());

                Log::info($data);
                // Validate
                $validator = Validator::make($request->all(), [
                    'name'              => 'required|string',
                    'description'       => 'nullable|string',
                    'price'             => 'required|numeric|min:0',
                    'purchase_price'    => 'nullable|numeric|min:0',
                    'category_id'       => 'required|exists:product_categories,id',
                    'sku'               => 'required|unique:products,sku',
                    'stock'             => 'required|integer|min:0',
                    'gender'            => 'required|in:Male,Female,Unisex,men,women,unisex',
                    'main_image'        => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'size_guide'        => 'nullable|file|mimes:pdf|max:5120',
                    'brand'       => 'required',

                    'subcategory_ids'   => 'nullable|array',
                    'subcategory_ids.*' => 'integer',

                    'variants'                  => 'required|array',
                    'variants.*.color'           => 'required|string',
                    'variants.*.color_name'      => 'required|string',
                    'variants.*.sizes' => 'required|array',
                    'variants.*.sizes.*' => 'required|exists:sizes,id', // Validate size IDs
                    'variants.*.size_prices' => 'required|array',
                    'variants.*.size_prices.*' => 'required|numeric|min:0',
                    'variants.*.images'          => 'required|array|min:1',
                    'variants.*.images.*'        => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validation error',
                        'errors' => $validator->errors()
                    ], 422);
                }


                // Create product
                $brand = Brands::where('slug', $data['brand'])->first();
                $product = Products::create([
                    'name'              => $data['name'],
                    'description'       => $data['description'] ?? null,
                    'price'             => $data['price'],
                    'purchase_price'    => $data['purchase_price'] ?? null,
                    'sku'               => $data['sku'],
                    'stock'             => $data['stock'],
                    'gender'            => $data['gender'],
                    'category_id'       => $data['category_id'],
                    'category_slug'     => $data['category_slug'] ?? '',
                    'main_image_name'   => $request->file('main_image')->store('products/main', 'public'),
                    'size_guide_name'   => $request->file('size_guide')?->store('products/size-guides', 'public'),
                    'brands_slug'         => $data['brand'],
                    'brands_id'          => $brand->id
                ]);

                //  Subcategories
                if (!empty($data['subcategory_ids'])) {
                    foreach ($data['subcategory_ids'] as $subcatId) {
                        DB::table('product_subcategories')->insert([
                            'product_id' => $product->id,
                            'subcategory_id' => $subcatId,
                        ]);
                    }
                }


                //  Variants with sizes & images
                foreach ($data['variants'] as $index => $variantData) {
                    $variant = $product->variants()->create([
                        'color' => $variantData['color'],
                        'color_name' => $variantData['color_name'],
                    ]);

                    // Sync sizes with prices
                    $sizePrices = [];
                    foreach ($variantData['sizes'] as $sizeId) {
                        if (isset($variantData['size_prices'][$sizeId])) {
                            $sizePrices[$sizeId] = ['price' => $variantData['size_prices'][$sizeId]];
                        }
                    }
                    $variant->sizes()->sync($sizePrices);

                    // Save images
                    foreach ($request->file("variants.$index.images") as $imageFile) {
                        $path = $imageFile->store('products/variants', 'public');
                        $variant->images()->create([
                            'image_name' => $path,
                            'image_type' => $imageFile->getClientMimeType(),
                            'image_size' => $imageFile->getSize(),
                        ]);
                    }
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Product created successfully',
                    'id' => $product->id,
                ], 201);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
    }


    public function edit(Request $request, $id)
    {
        try {
            $product = Products::with([
                'category',
                'subcategories',
                'variants',
                'variants.sizes',
                'variants.images'
            ])->findOrFail($id);

            // Prepare response data
            $response = [
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'purchase_price' => $product->purchase_price,
                    'sku' => $product->sku,
                    'stock' => $product->stock,
                    'gender' => $product->gender,
                    'status' => $product->status,
                    'category_id' => $product->category_id,
                    'category_slug' => $product->category->slug ?? '',
                    'main_image_url' => $product->main_image_name ? asset('storage/' . $product->main_image_name) : null,
                    'size_guide_url' => $product->size_guide_name ? asset('storage/' . $product->size_guide_name) : null,
                    'size_guide_name' => $product->size_guide_name ? basename($product->size_guide_name) : null,
                    'brand' => $product->brands_slug,
                ],
                'subcategories' => $product->subcategories->map(function ($subcat) {
                    return [
                        'id' => $subcat->id,
                        'title' => $subcat->title,
                    ];
                }),
                'variants' => $product->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'color' => $variant->color,
                        'color_name' => $variant->color_name,
                        'sizes' => $variant->sizes->map(function ($size) {
                            return [
                                'id' => $size->id,
                                'size_title' => $size->size_title,
                                'price' => $size->pivot->price // Include price from pivot table
                            ];
                        }),
                        'images' => $variant->images->map(function ($image) {
                            return [
                                'id' => $image->id,
                                'url' => asset('storage/' . $image->image_name),
                            ];
                        }),
                    ];
                }),
            ];

            return response()->json([
                'status' => true,
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($request->isMethod('put')) {
            try {
                $product = Products::with(['variants', 'subcategories'])->findOrFail($id);
                $data = Arr::undot($request->all());
                // Validation rules
                $rules = [
                    'name'              => 'required|string',
                    'description'       => 'nullable|string',
                    'price'             => 'required|numeric|min:0',
                    'purchase_price'    => 'nullable|numeric|min:0',
                    'category_id'       => 'required|exists:product_categories,id',
                    'sku'               => 'required|unique:products,sku,' . $id,
                    'stock'             => 'required|integer|min:0',
                    'gender'            => 'required|in:Male,Female,Unisex,men,women,unisex',
                    'main_image'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'size_guide'        => 'nullable|file|mimes:pdf|max:5120',
                    'brand'       => 'required',
                    'subcategory_ids'   => 'nullable|array',
                    'subcategory_ids.*' => 'integer',

                    'variants'                  => 'required|array',
                    'variants.*.id'             => 'nullable|exists:product_variants,id',
                    'variants.*.color'          => 'required|string',
                    'variants.*.color_name'     => 'required|string',
        'variants.*.sizes' => 'nullable|array',
        'variants.*.sizes.*.id' => 'required|exists:sizes,id',
        'variants.*.sizes.*.price' => 'required|numeric|min:0',
                    'variants.*.new_images'     => 'nullable|array',
                    'variants.*.new_images.*'   => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'variants.*.deleted_images' => 'nullable|array',
                    'variants.*.deleted_images.*' => 'required|exists:product_variant_images,id',
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validation error',
                        'errors' => $validator->errors()
                    ], 422);
                }

                $brand = Brands::where('slug', $data['brand'])->first();
                // Update product basic info
                $updateData = [
                    'name'              => $data['name'],
                    'description'       => $data['description'] ?? null,
                    'price'             => $data['price'],
                    // 'status'            => $data['status'],
                    'purchase_price'    => $data['purchase_price'] ?? null,
                    'sku'               => $data['sku'],
                    'stock'             => $data['stock'],
                    'gender'            => $data['gender'],
                    'category_id'       => $data['category_id'],
                    'category_slug'     => $data['category_slug'] ?? '',
                    'brands_slug'         => $data['brand'],
                    'brands_id'          => $brand->id
                ];

                // Handle main image update if provided
                if ($request->hasFile('main_image')) {
                    // Delete old image if exists
                    if ($product->main_image_name) {
                        Storage::disk('public')->delete($product->main_image_name);
                    }
                    $updateData['main_image_name'] = $request->file('main_image')->store('products/main', 'public');
                }

                // Handle size guide update if provided
                if ($request->hasFile('size_guide')) {
                    // Delete old size guide if exists
                    if ($product->size_guide_name) {
                        Storage::disk('public')->delete($product->size_guide_name);
                    }
                    $updateData['size_guide_name'] = $request->file('size_guide')->store('products/size-guides', 'public');
                }

                $product->update($updateData);

                // Update subcategories
                DB::table('product_subcategories')->where('product_id', $product->id)->delete();
                if (!empty($data['subcategory_ids'])) {
                    foreach ($data['subcategory_ids'] as $subcatId) {
                        DB::table('product_subcategories')->insert([
                            'product_id' => $product->id,
                            'subcategory_id' => $subcatId,
                        ]);
                    }
                }

                // Process variants
                $existingVariantIds = $product->variants->pluck('id')->toArray();
                $updatedVariantIds = [];

                foreach ($data['variants'] as $variantData) {
                    // Update or create variant
                    $variant = isset($variantData['id'])
                        ? ProductVariant::find($variantData['id'])
                        : $product->variants()->create([
                            'color' => $variantData['color'],
                            'color_name' => $variantData['color_name'],
                        ]);

                    $updatedVariantIds[] = $variant->id;

                    // Update variant details
                    $variant->update([
                        'color' => $variantData['color'],
                        'color_name' => $variantData['color_name'],
                    ]);

        // Sync sizes with pivot prices
        $syncData = [];
        foreach ($variantData['sizes'] as $size) {
            $syncData[$size['id']] = ['price' => $size['price']];
        }
        $variant->sizes()->sync($syncData);

                    // Delete marked images
                    if (!empty($variantData['deleted_images'])) {
                        $imagesToDelete = ProductVariantImage::whereIn('id', $variantData['deleted_images'])
                            ->where('variant_id', $variant->id)
                            ->get();

                        foreach ($imagesToDelete as $image) {
                            Storage::disk('public')->delete($image->image_name);
                            $image->delete();
                        }
                    }

                    // Add new images
                    if (!empty($variantData['new_images'])) {
                        foreach ($variantData['new_images'] as $imageFile) {
                            $path = $imageFile->store('products/variants', 'public');
                            $variant->images()->create([
                                'image_name' => $path,
                                'image_type' => $imageFile->getClientMimeType(),
                                'image_size' => $imageFile->getSize(),
                            ]);
                        }
                    }
                }

                // Delete variants that were removed
                $variantsToDelete = array_diff($existingVariantIds, $updatedVariantIds);
                if (!empty($variantsToDelete)) {
                    $deletedVariants = ProductVariant::whereIn('id', $variantsToDelete)->get();
                    foreach ($deletedVariants as $variant) {
                        foreach ($variant->images as $image) {
                            Storage::disk('public')->delete($image->image_name);
                        }
                        $variant->delete();
                    }
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Product updated successfully',
                    'id' => $product->id,
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
    }


    function delete(Request $request, $id)
    {

        if (Products::remove($id)) {
            $request->session()->flash('success', 'Product deleted successfully.');
            return redirect()->route('admin.products');
        }
    }

    function bulkActions(Request $request, $action)
    {
        if (($action != 'delete' && !Permissions::hasPermission('products', 'update')) || ($action == 'delete' && !Permissions::hasPermission('products', 'delete'))) {
            $request->session()->flash('error', 'Permission denied.');
            return redirect()->route('admin.dashboard');
        }

        $ids = $request->get('ids');
        if (is_array($ids) && !empty($ids)) {
            switch ($action) {
                case 'active':
                    Products::modifyAll($ids, [
                        'status' => 1
                    ]);
                    $message = count($ids) . ' records has been published.';
                    break;
                case 'inactive':
                    Products::modifyAll($ids, [
                        'status' => 0
                    ]);
                    $message = count($ids) . ' records has been unpublished.';
                    break;
                case 'delete':
                    Products::removeAll($ids);
                    $message = count($ids) . ' records has been deleted.';
                    break;
            }

            $request->session()->flash('success', $message);

            return Response()->json([
                'status' => 'success',
                'message' => $message,
            ], 200);
        } else {
            return Response()->json([
                'status' => 'error',
                'message' => 'Please select atleast one record.',
            ], 200);
        }
    }

    public function getSize($gender)
    {
        $sizes = Sizes::select(['id', 'size_title', 'from_cm', 'to_cm'])->whereType($gender)->get();
        return response()->json([
            'status' => true,
            'sizes' => $sizes,
        ]);
    }

    public function getSubCategory($categoryId)
    {
        $subCategory = ProductSubCategories::select(['id', 'title', 'status'])->whereStatus(1)->whereCategoryId($categoryId)->get();
        return response()->json([
            'status' => true,
            'subCategory' => $subCategory,
        ]);
    }

    public function fetchdata()
    {
        $category = ProductCategories::all();
        $subCategory = ProductSubCategories::all();
        $sizes = Sizes::all();
        $brands = Brands::all();
        $colors = Colours::all();

        return response()->json([
            'status' => true,
            'category' => $category,
            'subCategory' => $subCategory,
            'brands' => $brands,
            'sizes' => $sizes,
            'colors' => $colors ?? ''
        ]);
    }
}
