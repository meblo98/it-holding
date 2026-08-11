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
Route::post('/tva/toggle', [ShopController::class, 'toggleTva'])->name('shop.tva.toggle');

// Support Chat Routes (Client side)
Route::get('/chat/messages', [\App\Http\Controllers\ChatController::class, 'getMessages'])->name('chat.messages');
Route::post('/chat/send', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');

// Public Warranty Verification & QR Code Download
Route::get('/warranty/verify/{number}', [\App\Http\Controllers\WarrantyVerificationController::class, 'verify'])->name('warranty.verify');
Route::get('/warranty/{number}/qrcode/download', [\App\Http\Controllers\WarrantyVerificationController::class, 'downloadQrCode'])->name('warranty.qrcode.download');


Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:5,1');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->middleware('throttle:5,1');

    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register'])->middleware('throttle:3,1');
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'redirect.admin'])->group(function () {
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

    // Client Ticket / SAV Routes
    Route::get('/dashboard/tickets', [\App\Http\Controllers\DashboardController::class, 'tickets'])->name('dashboard.tickets');
    Route::get('/dashboard/tickets/create', [\App\Http\Controllers\DashboardController::class, 'createTicket'])->name('dashboard.tickets.create');
    Route::post('/dashboard/tickets', [\App\Http\Controllers\DashboardController::class, 'storeTicket'])->name('dashboard.tickets.store');
    Route::get('/dashboard/tickets/{ticket}', [\App\Http\Controllers\DashboardController::class, 'showTicket'])->name('dashboard.tickets.show');

    Route::post('/dashboard/settings/profile', [\App\Http\Controllers\DashboardController::class, 'updateProfile'])->name('dashboard.settings.updateProfile');
    Route::post('/dashboard/settings/address', [\App\Http\Controllers\DashboardController::class, 'updateAddress'])->name('dashboard.settings.updateAddress');
    Route::post('/dashboard/settings/password', [\App\Http\Controllers\DashboardController::class, 'updatePassword'])->name('dashboard.settings.updatePassword');
    Route::get('/dashboard/track', [\App\Http\Controllers\DashboardController::class, 'trackOrder'])->name('dashboard.track');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Services
    Route::middleware('permission:services')->group(function () {
        Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class);
    });

    // Portfolio
    Route::middleware('permission:projects')->group(function () {
        Route::resource('projects', \App\Http\Controllers\Admin\ProjectController::class);
    });

    // Blog
    Route::middleware('permission:posts')->group(function () {
        Route::resource('posts', \App\Http\Controllers\Admin\PostController::class);
    });

    // Boutique (Products, Categories, Brands)
    Route::middleware('permission:products')->group(function () {
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class);
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
        Route::post('products/{product}/duplicate', [\App\Http\Controllers\Admin\ProductController::class, 'duplicate'])->name('products.duplicate');
        Route::delete('products/{product}/images/{image}', [\App\Http\Controllers\Admin\ProductController::class, 'destroyImage'])->name('products.images.destroy');
    });

    // Commandes
    Route::middleware('permission:orders')->group(function () {
        Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show', 'update']);
    });

    // Devis (Quotes)
    Route::middleware('permission:quotes')->group(function () {
        Route::get('quotes/{quote}/print', [\App\Http\Controllers\Admin\QuoteController::class, 'print'])->name('quotes.print');
        Route::post('quotes/{quote}/convert', [\App\Http\Controllers\Admin\QuoteController::class, 'convert'])->name('quotes.convert');
        Route::get('quotes/{quote}/share', [\App\Http\Controllers\Admin\QuoteController::class, 'share'])->name('quotes.share');
        Route::resource('quotes', \App\Http\Controllers\Admin\QuoteController::class);
    });

    // Factures (Invoices)
    Route::middleware('permission:invoices')->group(function () {
        Route::get('invoices/{invoice}/print', [\App\Http\Controllers\Admin\InvoiceController::class, 'print'])->name('invoices.print');
        Route::get('invoices/{invoice}/share', [\App\Http\Controllers\Admin\InvoiceController::class, 'share'])->name('invoices.share');
        Route::post('invoices/{invoice}/credit-note', [\App\Http\Controllers\Admin\InvoiceController::class, 'createCreditNote'])->name('invoices.credit-note');
        Route::get('invoices/{invoice}/receipt', [\App\Http\Controllers\Admin\InvoiceController::class, 'printReceipt'])->name('invoices.receipt');
        Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class);
    });

    // Bons de livraison (Delivery Notes)
    Route::middleware('permission:delivery-notes')->group(function () {
        Route::get('delivery-notes/{delivery_note}/print', [\App\Http\Controllers\Admin\DeliveryNoteController::class, 'print'])->name('delivery-notes.print');
        Route::resource('delivery-notes', \App\Http\Controllers\Admin\DeliveryNoteController::class);
    });

    // Fournisseurs (Suppliers)
    Route::middleware('permission:suppliers')->group(function () {
        Route::resource('suppliers', \App\Http\Controllers\Admin\SupplierController::class);
    });

    // Gestion de stock
    Route::middleware('permission:stock')->group(function () {
        Route::get('stock', [\App\Http\Controllers\Admin\StockController::class, 'index'])->name('stock.index');
        Route::post('stock/{product}/adjust', [\App\Http\Controllers\Admin\StockController::class, 'adjust'])->name('stock.adjust');
    });

    // Clients CRM
    Route::middleware('permission:clients')->group(function () {
        Route::resource('clients', \App\Http\Controllers\Admin\ClientController::class);
        Route::post('clients/{client}/deposit', [\App\Http\Controllers\Admin\ClientController::class, 'deposit'])->name('clients.deposit');
        Route::post('clients/{client}/pay-debt', [\App\Http\Controllers\Admin\ClientController::class, 'payDebt'])->name('clients.pay-debt');
    });

    // Garanties
    Route::middleware('permission:warranties')->group(function () {
        Route::get('warranties/scanner', [\App\Http\Controllers\Admin\WarrantyController::class, 'scanner'])->name('warranties.scanner');
        Route::get('warranties/scan-search/{code?}', [\App\Http\Controllers\Admin\WarrantyController::class, 'scanSearch'])->name('warranties.scanSearch');
        Route::resource('warranties', \App\Http\Controllers\Admin\WarrantyController::class);
    });

    // SAV / Tickets
    Route::middleware('permission:tickets')->group(function () {
        Route::resource('tickets', \App\Http\Controllers\Admin\TicketController::class);
    });

    // Contrats de Maintenance
    Route::middleware('permission:contracts')->group(function () {
        Route::resource('contracts', \App\Http\Controllers\Admin\MaintenanceContractController::class);
    });

    // IT HOLDING CARE+
    Route::middleware('permission:care')->group(function () {
        Route::resource('care', \App\Http\Controllers\Admin\CareSubscriptionController::class);
    });

    // Expenses (Gestion des Dépenses)
    Route::middleware('permission:expenses')->group(function () {
        Route::resource('expenses', \App\Http\Controllers\Admin\ExpenseController::class);
    });

    // Finance & Bank
    Route::middleware('permission:finance')->group(function () {
        Route::get('finance', [\App\Http\Controllers\Admin\FinanceController::class, 'index'])->name('finance.index');
        Route::post('finance/bank-accounts', [\App\Http\Controllers\Admin\FinanceController::class, 'storeAccount'])->name('finance.bank-accounts.store');
        Route::post('finance/transactions', [\App\Http\Controllers\Admin\FinanceController::class, 'storeTransaction'])->name('finance.transactions.store');
        Route::post('finance/reconcile/{transaction}', [\App\Http\Controllers\Admin\FinanceController::class, 'reconcile'])->name('finance.reconcile');
    });

    // Administrative Reports (Rapports & Stats)
    Route::middleware('permission:reports')->group(function () {
        Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/sales', [\App\Http\Controllers\Admin\ReportController::class, 'sales'])->name('reports.sales');
        Route::get('reports/stocks', [\App\Http\Controllers\Admin\ReportController::class, 'stocks'])->name('reports.stocks');
        Route::get('reports/profits', [\App\Http\Controllers\Admin\ReportController::class, 'profits'])->name('reports.profits');
        Route::get('reports/suppliers', [\App\Http\Controllers\Admin\ReportController::class, 'suppliers'])->name('reports.suppliers');
        Route::get('reports/sav', [\App\Http\Controllers\Admin\ReportController::class, 'sav'])->name('reports.sav');
        Route::get('reports/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');
    });

    // Support Chat Routes (Admin side)
    Route::middleware('permission:chat')->group(function () {
        Route::get('chat', [\App\Http\Controllers\ChatController::class, 'adminIndex'])->name('chat.index');
        Route::get('chat/messages/{identifier}', [\App\Http\Controllers\ChatController::class, 'adminGetMessages'])->name('chat.messages');
        Route::post('chat/send/{identifier}', [\App\Http\Controllers\ChatController::class, 'adminSendMessage'])->name('chat.send');
    });

    // Équipe & Accès / Users (internal staff)
    Route::middleware('permission:users')->group(function () {
        Route::get('users/permissions', [\App\Http\Controllers\Admin\UserController::class, 'permissions'])->name('users.permissions');
        Route::post('users/permissions', [\App\Http\Controllers\Admin\UserController::class, 'updatePermissions'])->name('users.permissions.update');
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show']);
    });
});

// Public views for shared docs
Route::get('view/quote/{token}', [\App\Http\Controllers\Admin\QuoteController::class, 'publicView'])->name('quotes.public_view');
Route::get('view/invoice/{token}', [\App\Http\Controllers\Admin\InvoiceController::class, 'publicView'])->name('invoices.public_view');

// Storage Bypass Route for environments with blocked symlinks
Route::get('storage-bypass/{path}', function ($path) {
    if (str_contains($path, '..')) {
        abort(403, 'Unauthorized action.');
    }
    
    $fullPath = storage_path('app/public/' . $path);
    
    if (!file_exists($fullPath)) {
        abort(404);
    }
    
    return response()->file($fullPath);
})->where('path', '.*')->name('storage.bypass');



