<?php
// use App\Http\Controllers\Admin\TransactionController as AdminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;


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



Route::get('/', function () {
    return view('pages.index');
})->name('home');
//Commands 
Route::get('/run-schedule', function () {
    Artisan::call('schedule:run');
    return 'Scheduler executed successfully!';
});
Route::get('/db-fresh',function(){
    Artisan::call('migrate:fresh');
    return 'Migration completed!';
});
Route::get('/db-seed',function(){
    Artisan::call('db:seed');
    return 'Data seeded successfully!'
});

// Crone job command for scheduler
// /usr/local/bin/php /home/softjsbg/dailydonate.softicerastudent.com/artisan schedule:run >> /dev/null 2>&1

Route::get('/optimize-clear',function(){
    Artisan::call('optimize:clear');
    return 'Optimize clear executed successfully!';
});
Route::get('/optimize',function(){
    Artisan::call('optimize');
    return 'Optimize executed successfully!';
});
Route::get('/config-cache',function(){
    Artisan::call('config:cache');
    return 'Config cache executed successfully!';
});
Route::get('/config-clear',function(){
    Artisan::call('config:clear');
    return 'Config clear executed successfully!';
});
// Route::post('/webhook/stripe', function (Request $request) {
//     Log::info('Stripe Webhook Received:', $request->all());
//     return response()->json(['status' => 'success']);
// });
// Route::post('/webhook/stripe', [WebhookController::class, 'handleWebhook']);
Route::controller(StripePaymentController::class)->group(function () {
    Route::get('stripe', 'stripe');
    Route::post('stripe', 'stripePost')->name('stripe.post');
});

// Public routes (no auth required)
Route::prefix('auth')->group(function () {
    Route::get('login', function () {
        return view('pages.auth.login');
    })->name('login');
    Route::post('login-attempt', [AuthController::class, 'login'])->name('login-attempt');
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('user')->name('user.')->group(function () {


        // Subscription Routes
        Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
            Route::get('/', [SubscriptionController::class, 'index'])->name('index'); // Show all subscriptions
            Route::get('/{subscription}', [SubscriptionController::class, 'show'])->name('show'); // Show single subscription details
            Route::get('/{subscription}/cancel', [SubscriptionController::class, 'cancel_subscription'])->name('cancel'); // Cancel subscription
        });

        // Invoice Routes
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('index'); // Show all invoices
            Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show'); // Show single invoice details
        });

        // Transaction Routes
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/', [TransactionController::class, 'index'])->name('index'); // Show all transactions
            Route::get('/{transaction}', [TransactionController::class, 'show'])->name('show'); // Show single transaction details
        });
    });
    Route::prefix('admin')->name('admin.')->group(function () {

        Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
            Route::get('/', [SubscriptionController::class, 'index'])->name('index'); // Show all subscriptions
            Route::get('/{subscription}', [SubscriptionController::class, 'show'])->name('show'); // Show single subscription details
        });
        Route::prefix('donors')->name('donors.')->group(function () {
            Route::get('/', [DonorController::class, 'index'])->name('index'); // Show all subscriptions
            Route::get('/{subscription}', [DonorController::class, 'show'])->name('show'); // Show single subscription details
        });

        // Invoice Routes
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('index'); // Show all invoices
            Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show'); // Show single invoice details
        });

        // Transaction Routes
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/', [TransactionController::class, 'index'])->name('index'); // Show all transactions
            Route::get('/{transaction}', [TransactionController::class, 'show'])->name('show'); // Show single transaction details
        });
    });
});



Route::group(['prefix' => 'donation'], function () {
    Route::get('/regular', function () {
        return view('pages.donation.regular-donation');
    });
});




// Authenticated routes (require auth middleware)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
Route::group(['prefix' => 'auth'], function () {
    // Route::get('register', function () { return view('pages.auth.register'); });
});

// Route::group(['prefix' => 'error'], function () {
//     Route::get('404', function () {
//         return view('pages.error.404');
//     });
//     Route::get('500', function () {
//         return view('pages.error.500');
//     });
// });

// Route::get('/clear-cache', function () {
//     Artisan::call('cache:clear');
//     return "Cache is cleared";
// });

// // 404 for undefined routes
// Route::any('/{page?}', function () {
//     return View::make('pages.error.404');
// })->where('page', '.*');
