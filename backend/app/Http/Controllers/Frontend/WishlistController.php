<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Add product to wishlist
     */
    public function add(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id'
            ]);

            $user = Auth::user();

            // Check if already in wishlist
            $existing = Wishlist::where('user_id', $user->id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($existing) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product already in wishlist'
                ], 400);
            }

            $wishlist = Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Added to wishlist',
                'data' => $wishlist
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to add to wishlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's wishlist
     */
    public function index()
    {
        try {
            $user = Auth::user();

            $wishlist = Wishlist::with(['product.category', 'product.brands'])
                ->where('user_id', $user->id)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'product_slug' => $item->product->slug,
                        'product_price' => $item->product->price,
                        'product_image' => $item->product->main_image_name
                            ? asset('storage/' . $item->product->main_image_name)
                            : asset('images/default-product.jpg'),
                        'added_at' => $item->created_at,
                    ];
                });

            return response()->json([
                'status' => true,
                'data' => $wishlist
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch wishlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove from wishlist
     */
    public function remove($id)
    {
        try {
            $user = Auth::user();

            $wishlist = Wishlist::where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();

            $wishlist->delete();

            return response()->json([
                'status' => true,
                'message' => 'Removed from wishlist'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to remove from wishlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
