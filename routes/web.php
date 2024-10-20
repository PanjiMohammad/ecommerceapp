<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/test-job', function () {
//     $orderId = 1; // Replace with your test order ID
//     $productId = 1; // Replace with your test product ID
//     $trackingNumber = 'PNJ12345678'; // Replace with your test tracking number

//     \App\Jobs\UpdateOrderStatusToArrivedJob::dispatch($orderId, $productId, $trackingNumber);

//     return 'Job dispatched';
// });

// index
Route::get('/', 'Ecommerce\FrontController@index')->name('front.index');

// product
Route::get('/product', 'Ecommerce\FrontController@product')->name('front.product');
Route::get('/category/{slug}', 'Ecommerce\FrontController@categoryProduct')->name('front.category');
Route::get('/product/{slug}', 'Ecommerce\FrontController@show')->name('front.show_product');
Route::get('/search-suggestions', 'Ecommerce\FrontController@suggestions')->name('front.suggestions');

// promo
Route::get('/promo-product', 'Ecommerce\FrontController@promo')->name('front.promo'); 

Route::post('cart', 'Ecommerce\CartController@addToCart')->name('front.cart');
Route::get('/cart', 'Ecommerce\CartController@listCart')->name('front.list_cart');
Route::get('/cart/delete', 'Ecommerce\CartController@deleteCart')->name('front.delete_cart');
Route::post('/cart/update', 'Ecommerce\CartController@updateCart')->name('front.update_cart');
Route::get('/cart/setting-address', 'Ecommerce\CartController@settingAddress')->name('front.setting_address');

Route::get('/shipment', 'Ecommerce\CartController@checkout')->name('front.shipment');
Route::post('/checkout', 'Ecommerce\CartController@processCheckout')->name('front.store_checkout');
Route::post('/checkout/update-user', 'Ecommerce\CartController@updateAddress')->name('front.update_address');
Route::get('/checkout/{invoice}', 'Ecommerce\CartController@checkoutFinish')->name('front.finish_checkout');

Route::get('/getOngkir/{origin}/{destination}/{weight}/{courier}', 'Ecommerce\CartController@getOngkir')->name('front.cekOngkir');

// Login
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@postLogin')->name('post.newLogin');

// Register
Route::get('/register', 'Auth\LoginController@newRegister')->name('register');
Route::post('/register', 'Auth\LoginController@postRegister')->name('post.newRegister');

// Forgot Password
Route::get('/forgot-password', 'Auth\ResetPasswordController@forgotPasswordForm')->name('forgotPassword');
Route::post('/forgot-password', 'Auth\ResetPasswordController@sendPasswordResetLink')->name('sendPasswordResetLink');
Route::post('/reset-password-users', 'Auth\LoginController@resetPasswordUser')->name('resetPasswordUser');

// // Verifikasi Email
Route::get('verify/{token}', 'Seller\SellerController@verifySellerRegistration')->name('seller.verify');

// Logout
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// // Auth::routes();
// Route::match(['get', 'post'], '/register', function () {
//     return redirect('/login');
// })->name('register');

Route::group(['prefix' => 'administrator', 'middleware' => 'auth'], function() {
    
    // Admin -> Dashboard
    Route::get('/home', 'HomeController@index')->name('home');

     // Admin -> Content Setting
     Route::get('/content-setting', 'HomeController@contentSetting')->name('user.contentSetting');
     Route::put('/post-content-setting', 'HomeController@postContentSetting')->name('user.postContentSetting');

    // Admin -> Account Setting
    Route::get('/account-setting/{id}', 'HomeController@accountSetting')->name('user.acountSetting');
    Route::put('/post-account-setting', 'HomeController@postAccountSetting')->name('user.postAccountSetting');

    // Admin -> Konsumen
    Route::get('/consumen', 'CustomerController@index')->name('consumen.index');
    Route::get('/consumen/getDatatables', 'CustomerController@getDatatables')->name('consumen.getDatatables');
    Route::get('/consumen/add-consumen', 'CustomerController@create')->name('consumen.create');
    Route::post('/consumen/store-consumen', 'CustomerController@store')->name('consumen.store');
    Route::delete('/consumen/delete-consumen/{id}', 'CustomerController@destroy')->name('consumen.destroy');
    Route::get('/consumen/edit-consumen/{id}', 'CustomerController@edit')->name('consumen.edit');
    Route::put('/consumen/update-consumen', 'CustomerController@update')->name('consumen.update');

    // Admin -> Penjual
    Route::get('/seller', 'SellerController@index')->name('seller.newIndex');
    Route::get('/seller/getDatatables', 'SellerController@getDatatables')->name('seller.getDatatables');
    Route::get('/seller/add-seller', 'SellerController@create')->name('seller.create');
    Route::post('/seller/store-seller', 'SellerController@store')->name('seller.store');
    Route::delete('/seller/delete-seller/{id}', 'SellerController@destroy')->name('seller.destroy');
    Route::get('/seller/edit-seller/{id}', 'SellerController@edit')->name('seller.edit');
    Route::put('/seller/update-seller/{id}', 'SellerController@update')->name('seller.update');

    // Store
    // Route::get('/consumen', 'CustomerController@index')->name('consumen.index');
    // Route::get('/consumen/add-consumen', 'CustomerController@create')->name('customer.create');
    // Route::post('/consumen/store-consumen', 'CustomerController@store')->name('customer.store');
    // Route::delete('/consumen/delete-consumen/{id}', 'CustomerController@destroy')->name('customer.destroy');
    // Route::get('/consumen/edit-consumen/{id}', 'CustomerController@edit')->name('customer.edit');
    // Route::put('/consumen/update-consumen/{id}', 'CustomerController@update')->name('customer.update');

    // Admin -> Kategori
    Route::get('/category', 'CategoryController@index')->name('category.index');
    Route::get('/category/getDatatables', 'CategoryController@getDatatables')->name('category.getDatatables');
    Route::get('/category/add-category', 'CategoryController@create')->name('category.create');
    Route::post('/category/store-category', 'CategoryController@store')->name('category.store');
    Route::delete('/category/delete-category/{id}', 'CategoryController@destroy')->name('category.destroy');
    Route::get('/category/edit-category/{id}', 'CategoryController@edit')->name('category.edit');
    Route::put('/category/update-category/{id}', 'CategoryController@update')->name('category.update');

    // Admin -> Produk
    Route::get('/product', 'ProductController@index')->name('product.index');
    Route::get('/product/getDatatables', 'ProductController@getDatatables')->name('product.getDatatables');
    Route::get('/product/add-product', 'ProductController@create')->name('product.create');
    Route::post('/product/store-product', 'ProductController@store')->name('product.store');
    Route::delete('/product/delete-product/{id}', 'ProductController@destroy')->name('product.destroy');
    Route::get('/product/edit-product/{id}', 'ProductController@edit')->name('product.edit');
    Route::put('/product/update-product/{id}', 'ProductController@update')->name('product.update');
    Route::get('/product/bulk', 'ProductController@massUploadForm')->name('product.bulk'); 
    Route::post('/product/bulk', 'ProductController@massUpload')->name('product.saveBulk');
    Route::get('/product-detail/{id}', 'ProductController@show')->name('product.detail'); 

    // Admin -> Penarikan Dana
    Route::get('/withdrawals', 'HomeController@indexWithdraw')->name('withdraw.index');
    Route::get('/withdrawals/getDatatables', 'HomeController@indexWithdrawDatatables')->name('withdraw.getDatatables');
    Route::post('/update-withdrawals/{id}/{status}', 'HomeController@updateStatusWithdraw')->name('admin.updateWithdraw');

    // Admin -> Pesanan
    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', 'OrderController@index')->name('orders.index');
        Route::get('/getDatatables', 'OrderController@ordersGetDatatables')->name('orders.getDatatables');
        Route::get('/{invoice}', 'OrderController@view')->name('orders.view');
        Route::get('/detail-order/{invoice}', 'OrderController@showOrder')->name('orders.detailView');
        Route::get('/payment/{invoice}', 'OrderController@acceptPayment')->name('orders.approve_payment');
        Route::post('/shipping', 'OrderController@shippingOrder')->name('orders.shipping');
        Route::get('/return/{invoice}', 'OrderController@return')->name('orders.return');
        Route::post('/return', 'OrderController@approveReturn')->name('orders.approve_return');
        Route::delete('/{id}', 'OrderController@destroy')->name('orders.destroy');
    });

    // Admin -> Laporan 
    Route::group(['prefix' => 'reports'], function() {
        Route::match(['get', 'post'], '/', function () {
            return redirect('administrator/reports/order');
        });
        Route::get('/order', 'OrderController@orderReport')->name('report.order');
        Route::get('/order/getDatatables', 'OrderController@getDatatablesReport')->name('report.orderGetDatatables');
        Route::get('/reportorder/{daterange}', 'OrderController@orderReportPdf')->name('report.order_pdf');
        Route::get('/return', 'OrderController@returnReport')->name('report.return');
        Route::get('/return/getDatatables', 'OrderController@getDatatablesReportReturn')->name('report.orderReturnGetDatatables');
        Route::get('/reportreturn/{daterange}', 'OrderController@returnReportPdf')->name('report.return_pdf');
    });
});

Route::group(['prefix' => 'seller', 'namespace' => 'Seller', 'middleware' => 'seller'], function() {
    
    // Aktivasi Token
    // Route::get('/verify/{token}', 'Seller@verifySellerPassword')->name('seller.verify');

    // Penjual -> Account Setting
    Route::get('/account-setting/{id}', 'SellerController@accountSetting')->name('seller.setting');
    Route::put('/post-account-setting/{id}', 'SellerController@postAccountSetting')->name('seller.postSetting');

    // Penjual -> Dashboard
    Route::get('/home', 'SellerController@index')->name('seller.dashboard');
    Route::get('/home/getDatatables', 'SellerController@getDatatablesIndex')->name('seller.datatablesIndex');

    // Penjual -> Kategori
    Route::get('/category', 'CategoryController@index')->name('category.newIndex');
    Route::get('/category/add-category', 'CategoryController@create')->name('category.newCreate');
    Route::post('/category/store-category', 'CategoryController@store')->name('category.newStore');
    Route::delete('/category/delete-category/{id}', 'CategoryController@destroy')->name('category.newDestroy');
    Route::get('/category/edit-category/{id}', 'CategoryController@edit')->name('category.newEdit');
    Route::put('/category/update-category/{id}', 'CategoryController@update')->name('category.newUpdate');

    // Penjual -> Produk
    Route::get('/product', 'ProductController@index')->name('product.newIndex');
    Route::get('/product/datatables', 'ProductController@datatables')->name('product.datatables');
    Route::get('/product/add-product', 'ProductController@create')->name('product.newCreate');
    Route::post('/product/store-product', 'ProductController@store')->name('product.newStore');
    Route::delete('/product/delete-product/{id}', 'ProductController@destroy')->name('product.newDestroy');
    Route::get('/product/edit-product/{id}', 'ProductController@edit')->name('product.newEdit');
    Route::put('/product/update-product', 'ProductController@update')->name('product.newUpdate');
    Route::get('/product/bulk', 'ProductController@massUploadForm')->name('product.newBulk'); 
    Route::post('/product/bulk', 'ProductController@massUpload')->name('product.newSaveBulk');
    Route::get('/products/{id}', 'ProductController@show')->name('product.newShow');

    // Penjual -> Promo Produk
    Route::get('/promo-product', 'PromoController@index')->name('promoProduct.newIndex');
    Route::get('/promo-product/datatables', 'PromoController@datatables')->name('promoProduct.datatables');
    Route::get('/promo-product/add-promo-product', 'PromoController@create')->name('promoProduct.newCreate');
    Route::post('/promo-product/store-promo-product', 'PromoController@store')->name('promoProduct.newStore');
    Route::delete('/promo-product/delete-promo-product/{id}', 'PromoController@destroy')->name('promoProduct.newDestroy');
    Route::get('/promo-product/edit-promo-product/{id}', 'PromoController@edit')->name('promoProduct.newEdit');
    Route::put('/promo-product/update-promo-product', 'PromoController@update')->name('promoProduct.newUpdate');
    Route::get('/promo-product/bulk', 'PromoController@massUploadForm')->name('promoProduct.newBulk'); 
    Route::post('/promo-product/bulk', 'PromoController@massUpload')->name('promoProduct.newSaveBulk');
    Route::get('/promo-products/{id}', 'PromoController@show')->name('promoProduct.newShow');

    // Penjual -> Penarikan Uang
    Route::get('/withdraw', 'WithdrawalController@index')->name('withdrawals.index');
    Route::get('/withdraw/select-account', 'WithdrawalController@indexAccount')->name('withdrawals.account');
    Route::get('/withdraw/getDatatables', 'WithdrawalController@withdrawalsDatatables')->name('withdrawals.getDatatables');
    Route::get('/withdraw/getAccountDatatables', 'WithdrawalController@withdrawalsAccountDatatables')->name('withdrawals.getAccountDatatables');
    Route::post('/post-withdraw', 'WithdrawalController@store')->name('seller.postWithdraw');
    Route::post('/withdrawals/select', 'WithdrawalController@selectAccount')->name('withdrawals.select');
    Route::get('/withdraw/get-rekening/{id}', 'WithdrawalController@detailAccount')->name('withdrawals.detailAccount');
    Route::post('/withdrawals/store-withdrawals', 'WithdrawalController@storeWithdrawals')->name('withdrawals.update');
    Route::get('/withdraw/{daterange}', 'WithdrawalController@withdrawReport')->name('withdraw.pdf');
    Route::get('/withdraw/exportExcel/{daterange}', 'WithdrawalController@withdrawReportExcel')->name('withdraw.excel');
    Route::delete('/withdraw/remove-account/{id}', 'WithdrawalController@destroy')->name('withdrawals.removeAccount');

    // Penjual -> Pesanan
    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', 'OrderController@index')->name('orders.newIndex');
        Route::get('/finish', 'OrderController@orderFinishIndex')->name('orders.finishIndex');
        Route::get('/finishDatatables', 'OrderController@orderFinishDatatables')->name('orders.finishDatatables');
        Route::get('/datatables', 'OrderController@datatables')->name('orders.newDatatables');
        Route::get('/{invoice}', 'OrderController@view')->name('orders.newView');
        // Route::get('/order-cancelled/datatables', 'OrderController@orderCancelDatatables')->name('orders.cancelGetDatatables');
        Route::get('/payment/{invoice}/{product_id}', 'OrderController@acceptPayment')->name('orders.new_approve_payment');
        Route::post('/process', 'OrderController@processOrder')->name('orders.newProcess');
        Route::post('/shipping', 'OrderController@shippingOrder')->name('orders.newShipping');
        Route::get('/return/{invoice}/{product_id}', 'OrderController@return')->name('orders.newReturn');
        Route::get('/return-details/{invoice}/{product_id}', 'OrderController@newReturnDetails')->name('orders.newReturnDetails');
        Route::post('/return', 'OrderController@approveReturn')->name('orders.new_approve_return');
        Route::delete('/{id}', 'OrderController@destroy')->name('orders.newDestroy');
    });

    // Penjual -> Pesanan Dibatalkan
    Route::group(['prefix' => 'cancel'], function () {
        Route::get('/', 'OrderController@orderCancelIndex')->name('orders.cancelIndex');
        Route::get('/datatables', 'OrderController@orderCancelDatatables')->name('orders.cancelGetDatatables');
        Route::delete('/{id}', 'OrderController@destroyOrderCancel')->name('orders.cancelDelete');
    });

    // Penjual -> Pendapatan
    Route::group(['prefix' => 'incomes'], function () {
        Route::get('/', 'OrderController@incomeIndex')->name('orders.incomes');
        Route::get('/incomeDatatables', 'OrderController@incomeOnGoing')->name('orders.incomesGetDatatables');
        Route::get('/incomeDatatablesFinish', 'OrderController@incomeFinish')->name('orders.incomesFinishGetDatatables');
        Route::get('/incomeCancelDatatables', 'OrderController@incomeCancel')->name('orders.incomesCancelGetDatatables');
        Route::get('/incomeReturnDatatables', 'OrderController@incomeReturn')->name('orders.incomesReturnGetDatatables');
    });

    // Penjual -> Laporan
    Route::group(['prefix' => 'reports'], function() {
        Route::match(['get', 'post'], '/', function () {
            return redirect('seller/reports/order');
        });
        Route::get('/order', 'OrderController@orderReport')->name('report.newOrder');
        Route::get('/order/exportExcel/{daterange}', 'OrderController@exportExcelReport')->name('report.newReportExcel');
        Route::get('/order/datatables', 'OrderController@datatablesReport')->name('report.newReportDatatables');
        Route::get('/reportorder/{daterange}', 'OrderController@orderReportPdf')->name('new_report.order_pdf');
        Route::get('/return', 'OrderController@returnReport')->name('report.newReturn');
        Route::get('/return/exportExcel/{daterange}', 'OrderController@exportExcelReportReturn')->name('report.newReturnExcel');
        Route::get('/return/datatables', 'OrderController@datatablesReportReturn')->name('report.newReturnDatatables');
        Route::get('/reportreturn/{daterange}', 'OrderController@returnReportPdf')->name('new_report.return_pdf');
    });
    
});

Route::group(['prefix' => 'member', 'namespace' => 'Ecommerce'], function() {
    Route::match(['get', 'post'], '/', function () {
        return redirect('member/dashboard');
    });

    // login
    Route::get('login', 'LoginController@loginForm')->name('customer.login');
    Route::post('login', 'LoginController@login')->name('customer.post_login');

    // aktivasi token
    Route::get('verify/{token}', 'FrontController@verifyCustomerRegistration')->name('customer.verify');

    // registrasi
    Route::get('register', 'RegisterController@registerForm')->name('customer.register');
    Route::post('register', 'RegisterController@register')->name('customer.post_register');

    // forgot password
    Route::get('forgot-password', 'LoginController@forgotPassword')->name('customer.forgotPassword');
    Route::post('reset-password', 'LoginController@resetPassword')->name('customer.resetPassword');

    Route::group(['middleware' => 'customer'], function() {

        // index/dashboard
        Route::get('dashboard', 'LoginController@dashboard')->name('customer.dashboard');
       
        // history (& download pdf)
        Route::get('history', 'OrderController@historyOrders')->name('customer.history');
        Route::get('history/pdf/{invoice}', 'OrderController@historyOrdersPdf')->name('customer.history_pdf');
        
        // order
        Route::get('orders', 'OrderController@index')->name('customer.orders'); 
        Route::get('orders/{invoice}', 'OrderController@view')->name('customer.view_order');
        Route::get('orders/pdf/{invoice}', 'OrderController@pdf')->name('customer.order_pdf');
        
        Route::get('list-payment', 'OrderController@listPaymentIndex')->name('customer.listPayment'); 

        // order -> accept order
        Route::post('orders/accept', 'OrderController@acceptOrder')->name('customer.order_accept');

        // order -> return
        Route::get('orders/return/{invoice}/{product_id}', 'OrderController@returnForm')->name('customer.order_return');
        Route::put('orders/return', 'OrderController@processReturn')->name('customer.return');
        
        // order -> cancel
        Route::put('orders/cancel-order', 'OrderController@cancelOrder')->name('customer.cancel');

        // order -> payment
        Route::get('payment/{invoice}', 'OrderController@paymentForm')->name('customer.paymentForm');
        Route::post('payment/save', 'OrderController@storePayment')->name('customer.savePayment');

        // order -> rating
        Route::post('orders/ratings', 'OrderController@ratingStore')->name('customer.postRating');

        // account setting
        Route::get('setting', 'FrontController@customerSettingForm')->name('customer.settingForm');
        Route::post('setting', 'FrontController@customerUpdateProfile')->name('customer.setting');
        
        // wishlist
        Route::get('wishlists', 'WishlistController@index')->name('customer.wishlist');
        Route::post('wishlists', 'WishlistController@saveWishlist')->name('customer.save_wishlist');
        Route::delete('wishlists/{id}', 'WishlistController@deleteWishlist')->name('customer.deleteWishlist');
        
        // logout
        Route::get('logout', 'LoginController@logout')->name('customer.logout'); 
    });
});
