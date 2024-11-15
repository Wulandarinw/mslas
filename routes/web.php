<?php

use App\Http\Controllers\ApiController;
use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\OtpVerification;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\CancelPage;
use App\Livewire\CartPage;
use App\Livewire\CategoriesPage;
use App\Livewire\CheckoutPage;
use App\Livewire\HomePage;
use App\Livewire\MyAccountPage;
use App\Livewire\MyAddressPage;
use App\Livewire\MyOrderDetailPage;
use App\Livewire\MyOrdersPage;
use App\Livewire\Payment;
use App\Livewire\ProductDetailPage;
use App\Livewire\ProdutcsPage;
use App\Livewire\SuccessPage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class);
Route::get('/products', ProdutcsPage::class);
Route::get('/cart', CartPage::class);
Route::get('/products/{name}', ProductDetailPage::class);


Route::middleware('guest')->group(function(){
    Route::get('/login', LoginPage::class)->name('login');
    Route::get('/register', RegisterPage::class);
    Route::get('/forgot', ForgotPasswordPage::class)->name('password.requet');
    Route::get('/reset{token}', ResetPasswordPage::class)->name('password.reset');
    
    
});
Route::get('/verify-otp', OtpVerification::class)->name('otp.verify');

Route::middleware('auth')->group(function () {
    Route::get('/logout', function(){
    Auth::logout();
    return redirect('/');
    });
    Route::get('/checkout', CheckoutPage::class);
    Route::get('/my-orders', MyOrdersPage::class);
    Route::get('/my-account', MyAccountPage::class);
    Route::get('/my-address', MyAddressPage::class);
    Route::get('/my-orders/{order_id}', MyOrderDetailPage::class)->name('my-orders.show');
    Route::get('/success', SuccessPage::class)->name('success');
    Route::get('/cancel', CancelPage::class)->name('cancel');
    
});

Route::post('/payment', [Payment::class, 'show'])->name('payment.show');
Route::post('/update-payment', [Payment::class, 'show'])->name('payment.show');
Route::post('/payment/notification', [Payment::class, 'notification'])->name('payment.notification');
Route::get('/payment/success/{orderNumber}', [Payment::class, 'success'])->name('payment.success');
Route::get('/payment/pending/{orderNumber}', [Payment::class, 'pending'])->name('payment.pending');
Route::get('/payment/error/{orderNumber}', [Payment::class, 'error'])->name('payment.error');
Route::get('/payment/cancel/{orderNumber}', [Payment::class, 'cancel'])->name('payment.cancel');

Route::get('provinces', [ApiController::class, 'getProvince'])->name('api.provinces');
Route::get('kabupatens/{provinsi}', [ApiController::class, 'getKabupaten'])->name('api.kabupatens');
Route::get('kecamatans/{kabupaten}', [ApiController::class, 'getKecamatan'])->name('api.kecamatans');
Route::get('kelurahans/{kecamatan}', [ApiController::class, 'getKelurahan'])->name('api.kelurahans');
Route::get('kodepos/{kelurahan}', [ApiController::class, 'getKodePos'])->name('api.kodepos');