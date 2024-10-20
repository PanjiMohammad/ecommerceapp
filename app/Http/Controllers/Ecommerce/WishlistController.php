<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Wishlist;
use App\Product;
use DB;
use Carbon\Carbon;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::with(['product'])->where('customer_id', auth()->guard('customer')->user()->id)->orderBy('created_at', 'DESC')->paginate(10);

        // Collect seller IDs from the products
        $productIds = $wishlists->pluck('product_id')->toArray();

        // Get sellers and their addresses
        $products = Product::whereIn('id', $productIds)->get();
        $productNames = $products->pluck('name', 'id')->toArray();
        $productStocks = $products->pluck('stock', 'id')->toArray();

        // Add district and city names to each product
        $wishlist = $wishlists->getCollection()->transform(function ($wishlist) use ($products, $productNames, $productStocks) {
            $product = $products->firstWhere('id', $wishlist->product_id);
            if ($product) {
                $wishlist->product_name = $productNames[$product->id] ?? '-';
                $wishlist->product_stock = $productStocks[$product->id] ?? '-';
            } else {
                $wishlist->product_name = '-';
                $wishlist->product_name = '0';
            }

            $wishlist->formatted_created_at = Carbon::parse($wishlist->created_a)->locale('id')->translatedFormat('l, d F Y');
            return $wishlist;
        });

        return view('ecommerce.wishlists.index', compact('wishlists', 'wishlist'));
    }

    public function saveWishlist(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|exists:products,id'
        ]);

        Wishlist::create([
            'customer_id' => auth()->guard('customer')->user()->id,
            'product_id' => $request->product_id
        ]);

        return response()->json(['success' => 'Produk berhasil ditambahkan ke daftar keinginan']);
    }

    public function deleteWishlist($id)
    {
        $wishlist = Wishlist::find($id);
        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['success' => 'Produk berhasil dihapus dari daftar keinginan']);
        }

        return response()->json(['error' => 'Produk di Daftar Keinginan ini Tidak Ada']);
    }
        
}
