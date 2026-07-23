<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/portfolio/{slug}', [PortfolioController::class, 'show'])->name('portfolio.show');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/cart', [ShopController::class, 'cart'])->name('shop.cart');
Route::get('/checkout', [ShopController::class, 'checkout'])->name('shop.checkout')->middleware('auth');
Route::post('/checkout', [ShopController::class, 'placeOrder'])->name('shop.placeOrder')->middleware('auth');
Route::get('/thanks/{order}', [ShopController::class, 'thanks'])->name('shop.thanks');
Route::post('/cart/add/{id}', [ShopController::class, 'addToCart'])->name('shop.addToCart');
Route::post('/cart/update', [ShopController::class, 'updateCart'])->name('shop.updateCart');
Route::get('/remove-from-cart/{id}', [ShopController::class, 'removeFromCart'])->name('shop.removeFromCart');
Route::post('/shop/quote-request', [ShopController::class, 'requestQuote'])->name('shop.requestQuote')->middleware('auth');
Route::post('/promo/apply', [ShopController::class, 'applyPromoCode'])->name('shop.promo.apply');
Route::post('/promo/remove', [ShopController::class, 'removePromoCode'])->name('shop.promo.remove');


Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:5,1');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->middleware('throttle:5,1');

    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register'])->middleware('throttle:3,1');
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/orders', [\App\Http\Controllers\DashboardController::class, 'orders'])->name('dashboard.orders');
    Route::get('/dashboard/orders/{order}', [\App\Http\Controllers\DashboardController::class, 'showOrder'])->name('dashboard.orders.show');
    Route::get('/dashboard/settings', [\App\Http\Controllers\DashboardController::class, 'settings'])->name('dashboard.settings');
    Route::get('/dashboard/warranties', [\App\Http\Controllers\DashboardController::class, 'warranties'])->name('dashboard.warranties');
    
    // Partner Promo & Commission Routes
    Route::get('/dashboard/partner', [\App\Http\Controllers\DashboardController::class, 'partner'])->name('dashboard.partner');
    Route::post('/dashboard/partner/promo', [\App\Http\Controllers\DashboardController::class, 'generatePromoCode'])->name('dashboard.partner.promo.generate');
    
    // Client Savings Routes
    Route::get('/dashboard/savings', [\App\Http\Controllers\DashboardController::class, 'savings'])->name('dashboard.savings');
    Route::get('/dashboard/savings/create', [\App\Http\Controllers\DashboardController::class, 'createSavingPlan'])->name('dashboard.savings.create');
    Route::post('/dashboard/savings', [\App\Http\Controllers\DashboardController::class, 'storeSavingPlan'])->name('dashboard.savings.store');
    Route::get('/dashboard/savings/{savingPlan}', [\App\Http\Controllers\DashboardController::class, 'showSavingPlan'])->name('dashboard.savings.show');
    Route::post('/dashboard/savings/{savingPlan}/deposit', [\App\Http\Controllers\DashboardController::class, 'depositSavingPlan'])->name('dashboard.savings.deposit');
    Route::post('/dashboard/savings/{savingPlan}/withdraw', [\App\Http\Controllers\DashboardController::class, 'withdrawSavingPlan'])->name('dashboard.savings.withdraw');

    Route::post('/dashboard/settings/profile', [\App\Http\Controllers\DashboardController::class, 'updateProfile'])->name('dashboard.settings.updateProfile');
    Route::post('/dashboard/settings/address', [\App\Http\Controllers\DashboardController::class, 'updateAddress'])->name('dashboard.settings.updateAddress');
    Route::post('/dashboard/settings/password', [\App\Http\Controllers\DashboardController::class, 'updatePassword'])->name('dashboard.settings.updatePassword');
    Route::get('/dashboard/track', [\App\Http\Controllers\DashboardController::class, 'trackOrder'])->name('dashboard.track');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class);
    Route::resource('projects', \App\Http\Controllers\Admin\ProjectController::class);
    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class);
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class);
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::delete('products/{product}/images/{image}', [\App\Http\Controllers\Admin\ProductController::class, 'destroyImage'])->name('products.images.destroy');
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show', 'update']);

    // Quotes & Invoices
    Route::get('quotes/{quote}/print', [\App\Http\Controllers\Admin\QuoteController::class, 'print'])->name('quotes.print');
    Route::post('quotes/{quote}/convert', [\App\Http\Controllers\Admin\QuoteController::class, 'convert'])->name('quotes.convert');
    Route::get('quotes/{quote}/share', [\App\Http\Controllers\Admin\QuoteController::class, 'share'])->name('quotes.share');
    Route::resource('quotes', \App\Http\Controllers\Admin\QuoteController::class);

    Route::get('invoices/{invoice}/print', [\App\Http\Controllers\Admin\InvoiceController::class, 'print'])->name('invoices.print');
    Route::get('invoices/{invoice}/share', [\App\Http\Controllers\Admin\InvoiceController::class, 'share'])->name('invoices.share');
    Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class);

    Route::get('delivery-notes/{delivery_note}/print', [\App\Http\Controllers\Admin\DeliveryNoteController::class, 'print'])->name('delivery-notes.print');
    Route::resource('delivery-notes', \App\Http\Controllers\Admin\DeliveryNoteController::class);
    Route::resource('suppliers', \App\Http\Controllers\Admin\SupplierController::class);

    // Stock Management
    Route::get('stock', [\App\Http\Controllers\Admin\StockController::class, 'index'])->name('stock.index');
    Route::post('stock/{product}/adjust', [\App\Http\Controllers\Admin\StockController::class, 'adjust'])->name('stock.adjust');

    // Users (internal staff) — admin only
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show']);

    // Clients CRM
    Route::resource('clients', \App\Http\Controllers\Admin\ClientController::class);

    // Warranties
    Route::resource('warranties', \App\Http\Controllers\Admin\WarrantyController::class);

    // SAV / Tickets
    Route::resource('tickets', \App\Http\Controllers\Admin\TicketController::class);

    // Contrats de Maintenance
    Route::resource('contracts', \App\Http\Controllers\Admin\MaintenanceContractController::class);

    // IT HOLDING CARE+
    Route::resource('care', \App\Http\Controllers\Admin\CareSubscriptionController::class);

    // Finance & Bank
    Route::get('finance', [\App\Http\Controllers\Admin\FinanceController::class, 'index'])->name('finance.index');
    Route::post('finance/bank-accounts', [\App\Http\Controllers\Admin\FinanceController::class, 'storeAccount'])->name('finance.bank-accounts.store');
    Route::post('finance/transactions', [\App\Http\Controllers\Admin\FinanceController::class, 'storeTransaction'])->name('finance.transactions.store');
    Route::post('finance/reconcile/{transaction}', [\App\Http\Controllers\Admin\FinanceController::class, 'reconcile'])->name('finance.reconcile');

    // Client Wallet & Finance Actions
    Route::post('clients/{client}/deposit', [\App\Http\Controllers\Admin\ClientController::class, 'deposit'])->name('clients.deposit');
    Route::post('clients/{client}/pay-debt', [\App\Http\Controllers\Admin\ClientController::class, 'payDebt'])->name('clients.pay-debt');

    // Invoices extra actions (Avoir & Receipt)
    Route::post('invoices/{invoice}/credit-note', [\App\Http\Controllers\Admin\InvoiceController::class, 'createCreditNote'])->name('invoices.credit-note');
    Route::get('invoices/{invoice}/receipt', [\App\Http\Controllers\Admin\InvoiceController::class, 'printReceipt'])->name('invoices.receipt');

    // Administrative Reports
    Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/sales', [\App\Http\Controllers\Admin\ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/stocks', [\App\Http\Controllers\Admin\ReportController::class, 'stocks'])->name('reports.stocks');
    Route::get('reports/profits', [\App\Http\Controllers\Admin\ReportController::class, 'profits'])->name('reports.profits');
    Route::get('reports/suppliers', [\App\Http\Controllers\Admin\ReportController::class, 'suppliers'])->name('reports.suppliers');
    Route::get('reports/sav', [\App\Http\Controllers\Admin\ReportController::class, 'sav'])->name('reports.sav');
    Route::get('reports/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');
});

// Public views for shared docs
Route::get('view/quote/{token}', [\App\Http\Controllers\Admin\QuoteController::class, 'publicView'])->name('quotes.public_view');
Route::get('view/invoice/{token}', [\App\Http\Controllers\Admin\InvoiceController::class, 'publicView'])->name('invoices.public_view');



