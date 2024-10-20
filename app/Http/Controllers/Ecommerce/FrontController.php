<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Promo;
use App\Category;
use App\Customer;
use App\Seller;
use App\Province;
use App\City;
use App\District;
use App\Wishlist;
use Carbon\Carbon;

class FrontController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'DESC')->where('status', 1)->paginate(15);
        $promos = Product::orderBy('created_at', 'DESC')->where('status', 1)->where('type', 'promo')->where('end_date', '>', Carbon::now())->get();

        // Collect seller IDs from the products
        $sellerIds = $products->pluck('seller_id')->toArray();

        // Get sellers and their addresses
        $sellers = Seller::whereIn('id', $sellerIds)->get();
        $districtIds = $sellers->pluck('district_id')->toArray();

        // Get district names and associated city IDs
        $districts = District::whereIn('id', $districtIds)->get();
        $districtNames = $districts->pluck('name', 'id')->toArray();
        $cityIds = $districts->pluck('city_id')->toArray();

        // Get city names
        $cities = City::whereIn('id', $cityIds)->get();
        $cityNames = $cities->pluck('name', 'id')->toArray();

        $products->getCollection()->transform(function ($product) use ($sellers, $districtNames, $cityNames) {
            $seller = $sellers->firstWhere('id', $product->seller_id);
            if ($seller) {
                $product->district_name = $districtNames[$seller->district_id] ?? '-';
                $district = District::find($seller->district_id);
                if ($district) {
                    $product->city_name = $cityNames[$district->city_id] ?? '-';
                } else {
                    $product->city_name = '-';
                }
            } else {
                $product->district_name = '-';
                $product->city_name = '-';
            }
            return $product;
        });

        return view('ecommerce.index', compact('products', 'promos'));
    }

    public function product()
    {
        $query = Product::where('status', 1);

        if (request()->q != '') {
            $query->where('name', 'LIKE', '%' . request()->q . '%');
        }

        if (request()->price != '') {
            $query->orderBy('price', request()->price);
        }

        if (request()->price === 'promo_price') {
            $query->orderBy('promo_price', request()->price);
        }

        $products = $query->paginate(12);
        $hasProducts = $products->count() > 0;

        $sellerIds = $products->pluck('seller_id')->toArray();
        $sellers = Seller::whereIn('id', $sellerIds)->get();
        
        $districtIds = $sellers->pluck('district_id')->toArray();
        $districts = District::whereIn('id', $districtIds)->get();
        $districtNames = $districts->pluck('name', 'id')->toArray();
        
        $cityIds = $districts->pluck('city_id')->toArray();
        $cities = City::whereIn('id', $cityIds)->get();
        $cityNames = $cities->pluck('name', 'id')->toArray();

        $products->getCollection()->transform(function ($product) use ($sellers, $districtNames, $cityNames) {
            $seller = $sellers->firstWhere('id', $product->seller_id);
            if ($seller) {
                $product->district_name = $districtNames[$seller->district_id] ?? '-';
                $district = District::find($seller->district_id);
                if ($district) {
                    $product->city_name = $cityNames[$district->city_id] ?? '-';
                } else {
                    $product->city_name = '-';
                }
            } else {
                $product->district_name = '-';
                $product->city_name = '-';
            }
            return $product;
        });

        return view('ecommerce.product', compact('products', 'hasProducts'));
    }

    public function categoryProduct($slug)
    {
        if (Category::whereSlug($slug)->exists()){
            $query = Category::where('slug', $slug)->first()->product()->where('status', 1);
        
            if (request()->q != '') {
                $query->where('name', 'LIKE', '%' . request()->q . '%');
            }

            if (request()->price != '') {
                $query->orderBy('price', request()->price);
            } else {
                $query->orderBy('created_at', 'DESC');
            }

            $products = $query->paginate(12);

            $hasProducts = $products->count() > 0;
    
            $sellerIds = $products->pluck('seller_id')->toArray();
            $sellers = Seller::whereIn('id', $sellerIds)->get();
            
            $districtIds = $sellers->pluck('district_id')->toArray();
            $districts = District::whereIn('id', $districtIds)->get();
            $districtNames = $districts->pluck('name', 'id')->toArray();
            
            $cityIds = $districts->pluck('city_id')->toArray();
            $cities = City::whereIn('id', $cityIds)->get();
            $cityNames = $cities->pluck('name', 'id')->toArray();

            $products->getCollection()->transform(function ($product) use ($sellers, $districtNames, $cityNames) {
                $seller = $sellers->firstWhere('id', $product->seller_id);
                if ($seller) {
                    $product->district_name = $districtNames[$seller->district_id] ?? '-';
                    $district = District::find($seller->district_id);
                    if ($district) {
                        $product->city_name = $cityNames[$district->city_id] ?? '-';
                    } else {
                        $product->city_name = '-';
                    }
                } else {
                    $product->district_name = '-';
                    $product->city_name = '-';
                }
                return $product;
            });

            return view('ecommerce.product', compact('products', 'hasProducts'));
        }else{
            return redirect()->back();
        }
    }

    public function suggestions(Request $request)
    {
        // Validate the query input
        $request->validate([
            'query' => 'required|string|max:255'
        ]);

        // Retrieve the query and fetch matching products
        $query = $request->input('query');
        
        $suggestions = Product::where('name', 'like', "%{$query}%")
            ->select('id', 'name', 'slug') // Select only necessary fields
            ->limit(10)                    // Limit the number of suggestions
            ->get();

        // Ensure the result is returned as JSON array
        return response()->json($suggestions->toArray());
    }

    public function promo()
    {
        $promos = Product::orderBy('created_at', 'DESC')->where('type', 'promo')->where('status', 1)->where('end_date', '>', now())->get();

        $sellerIds = $promos->pluck('seller_id')->toArray();
        $sellers = Seller::whereIn('id', $sellerIds)->get();
        
        $districtIds = $sellers->pluck('district_id')->toArray();
        $districts = District::whereIn('id', $districtIds)->get();
        $districtNames = $districts->pluck('name', 'id')->toArray();
        
        $cityIds = $districts->pluck('city_id')->toArray();
        $cities = City::whereIn('id', $cityIds)->get();
        $cityNames = $cities->pluck('name', 'id')->toArray();

        $promos->transform(function ($promo) use ($sellers, $districtNames, $cityNames) {
            $seller = $sellers->firstWhere('id', $promo->seller_id);
            if ($seller) {
                $promo->district_name = $districtNames[$seller->district_id] ?? '-';
                $district = District::find($seller->district_id);
                if ($district) {
                    $promo->city_name = $cityNames[$district->city_id] ?? '-';
                } else {
                    $promo->city_name = '-';
                }
            } else {
                $promo->district_name = '-';
                $promo->city_name = '-';
            }

            return $promo;
        });

        return view('ecommerce.promos', compact('promos'));
    }

    public function show($slug)
    {
        if (Product::whereSlug($slug)->exists()){
            $product = Product::with(['category'])->where('slug', $slug)->first();
            if(auth()->guard('customer')->check()){
                $wishlist = Wishlist::where('customer_id', auth()->guard('customer')->user()->id)
                            ->where('product_id', $product->id)->first();
                return view('ecommerce.show', compact('product', 'wishlist'));
            }else{
                return view('ecommerce.show', compact('product'));
            }
        }else{
            return redirect()->back();
        }
    }

    public function verifyCustomerRegistration($token)
    {
        $customer = Customer::where('activate_token', $token)->first();
        if ($customer) {

            $customer->update([
                'activate_token' => null,
                'status' => 1
            ]);
            return redirect(route('customer.login'))->with(['success' => 'Berhasil verifikasi, Silahkan Login']);
        }
        return redirect(route('customer.login'))->with(['error' => 'Invalid Verifikasi Token']);
    }

    public function customerSettingForm()
    {
        $customer = auth()->guard('customer')->user()->load('district');
        $provinces = Province::orderBy('name', 'ASC')->get();
        return view('ecommerce.setting', compact('customer', 'provinces'));
    }

    public function customerUpdateProfile(Request $request)
    {
        $validator = Validator::make($request, [
            'name' => 'required|string|max:100',
            'phone_number' => 'required|max:15',
            'address' => 'required|string',
            'gender' => 'required',
            'district_id' => 'required|exists:districts,id',
            'password' => 'nullable|string|min:5'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validasi gagal, harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 400);
        }

        $user = auth()->guard('customer')->user();
        $data = $request->only('name', 'phone_number', 'address', 'gender', 'district_id');

        if ($request->password != '') {
            $data['password'] = $request->password;
        }
        
        if($user) {
            $user->update($data);
            return response()->json(['success' => 'Profil berhasil diperbaharui'], 200);
        } else {
            return response()->json(['error' => 'Terjadi Kesalahan, Silahkan Coba Lagi.'], 404);
        }
        // return redirect()->back()->with(['success' => 'Profil berhasil diperbaharui']);
    }
}
