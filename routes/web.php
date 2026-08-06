<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\IngredientController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\ShiftController as AdminShiftController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\PrinterSettingsController;
use App\Http\Controllers\Admin\OperationalCostController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\ProfitLossController;
use App\Http\Controllers\Admin\WasteController;
use App\Http\Controllers\Admin\StockOpnameController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Pos\BaristaDashboardController;
use App\Http\Controllers\Pos\PosController;
use App\Http\Controllers\Pos\ReceiptController;
use App\Http\Controllers\Pos\OrderQueueController;
use App\Http\Controllers\Pos\BaristaReportController;
use App\Http\Controllers\Pos\AttendanceController;
use App\Http\Controllers\Pos\ShiftController;
use App\Http\Controllers\Pos\PosAuthController;
use App\Http\Controllers\Pos\CashExpenseController;
use App\Http\Controllers\Pos\StockOpnameController as PosStockOpnameController;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('pos.dashboard');
    }
    return redirect()->route('login');
});

// AUTH
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ============================================
// ADMIN ROUTES (middleware: auth, role:admin)
// ============================================
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', ProductController::class);
    Route::patch('products/{product}/toggle-availability', [ProductController::class, 'toggleAvailability'])->name('products.toggle');
    Route::resource('categories', CategoryController::class);

    Route::get('ingredients', [IngredientController::class, 'index'])->name('ingredients.index');
    Route::get('ingredient-categories', [IngredientController::class, 'categoryIndex'])->name('ingredient-categories.index');
    Route::post('ingredient-categories', [IngredientController::class, 'storeCategory'])->name('ingredient-categories.store');
    Route::patch('ingredient-categories/{ingredientCategory}', [IngredientController::class, 'updateCategory'])->name('ingredient-categories.update');
    Route::delete('ingredient-categories/{ingredientCategory}', [IngredientController::class, 'destroyCategory'])->name('ingredient-categories.destroy');
    Route::post('ingredients', [IngredientController::class, 'storeIngredient'])->name('ingredients.store');
    Route::patch('ingredients/{ingredient}', [IngredientController::class, 'updateIngredient'])->name('ingredients.update');
    Route::delete('ingredients/{ingredient}', [IngredientController::class, 'destroyIngredient'])->name('ingredients.destroy');
    Route::post('ingredient-purchases', [IngredientController::class, 'storePurchase'])->name('ingredient-purchases.store');

    Route::get('stocks', [StockController::class, 'index'])->name('stocks.index');

    Route::resource('employees', EmployeeController::class);
    Route::patch('employees/{user}/toggle-active', [EmployeeController::class, 'toggleActive'])->name('employees.toggle');
    Route::get('employees/{user}/activity', [EmployeeController::class, 'activityLog'])->name('employees.activity');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/products', [ReportController::class, 'products'])->name('reports.products');
    Route::get('reports/employees', [ReportController::class, 'employees'])->name('reports.employees');
    Route::get('reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

    Route::resource('vouchers', VoucherController::class);
    Route::post('vouchers/generate-batch', [VoucherController::class, 'generateBatch'])->name('vouchers.generate-batch');
    Route::delete('vouchers/batch', [VoucherController::class, 'destroyBatch'])->name('vouchers.destroy-batch');

    // Promotions
    Route::get('promotions', [PromotionController::class, 'index'])->name('promotions.index');
    Route::post('promotions', [PromotionController::class, 'store'])->name('promotions.store');
    Route::get('promotions/{promotion}/edit', [PromotionController::class, 'edit'])->name('promotions.edit');
    Route::patch('promotions/{promotion}', [PromotionController::class, 'update'])->name('promotions.update');
    Route::patch('promotions/{promotion}/toggle', [PromotionController::class, 'toggleActive'])->name('promotions.toggle');
    Route::delete('promotions/{promotion}', [PromotionController::class, 'destroy'])->name('promotions.destroy');

    Route::get('shifts', [AdminShiftController::class, 'index'])->name('shifts.index');
    Route::get('shifts/{shift}', [AdminShiftController::class, 'show'])->name('shifts.show');

    Route::get('attendances', [AdminAttendanceController::class, 'index'])->name('attendances.index');
    
    Route::get('stock-reports', [\App\Http\Controllers\Admin\StockReportController::class, 'index'])->name('stock-reports.index');
    Route::patch('stock-reports/{stockReport}/resolve', [\App\Http\Controllers\Admin\StockReportController::class, 'resolve'])->name('stock-reports.resolve');

    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::resource('printer-settings', PrinterSettingsController::class)->except('show');

    // Operational Costs
    Route::get('operational-costs', [OperationalCostController::class, 'index'])->name('operational-costs.index');
    Route::post('operational-costs', [OperationalCostController::class, 'store'])->name('operational-costs.store');
    Route::patch('operational-costs/{operationalCost}', [OperationalCostController::class, 'update'])->name('operational-costs.update');
    Route::delete('operational-costs/{operationalCost}', [OperationalCostController::class, 'destroy'])->name('operational-costs.destroy');
    Route::post('operational-costs/copy', [OperationalCostController::class, 'copyFromPreviousMonth'])->name('operational-costs.copy');

    // Payroll
    Route::get('payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::post('payroll', [PayrollController::class, 'store'])->name('payroll.store');
    Route::patch('payroll/{payroll}', [PayrollController::class, 'update'])->name('payroll.update');
    Route::patch('payroll/{payroll}/mark-paid', [PayrollController::class, 'markAsPaid'])->name('payroll.mark-paid');
    Route::delete('payroll/{payroll}', [PayrollController::class, 'destroy'])->name('payroll.destroy');

    // Profit & Loss
    Route::get('profit-loss', [ProfitLossController::class, 'index'])->name('profit-loss.index');

    // Waste Tracking
    Route::get('waste', [WasteController::class, 'index'])->name('waste.index');
    Route::post('waste', [WasteController::class, 'store'])->name('waste.store');

    // Stock Opname
    Route::get('stock-opname', [StockOpnameController::class, 'index'])->name('stock-opname.index');
});

// ============================================
// BARISTA / POS ROUTES (middleware: auth)
// ============================================
Route::prefix('pos')->middleware(['auth'])->name('pos.')->group(function () {
    Route::get('/dashboard', [BaristaDashboardController::class, 'index'])->name('dashboard');

    Route::get('/', [PosController::class, 'index'])->name('index');
    Route::get('/products', [PosController::class, 'products'])->name('products');
    Route::post('/orders', [PosController::class, 'createOrder'])->name('orders.create');
    Route::patch('/orders/{order}/hold', [PosController::class, 'holdOrder'])->name('orders.hold');
    Route::get('/orders/held', [PosController::class, 'heldOrders'])->name('orders.held');
    Route::patch('/orders/{order}/resume', [PosController::class, 'resumeOrder'])->name('orders.resume');
    Route::patch('/orders/{order}/pay', [PosController::class, 'processPayment'])->name('orders.pay');
    Route::patch('/orders/{order}/cancel', [PosController::class, 'cancelOrder'])->name('orders.cancel');
    Route::post('/vouchers/validate', [PosController::class, 'validateVoucher'])->name('vouchers.validate');

    Route::get('/orders/{order}/receipt', [ReceiptController::class, 'show'])->name('receipt.show');
    Route::get('/orders/{order}/receipt/print', [ReceiptController::class, 'print'])->name('receipt.print');

    Route::get('/queue', [OrderQueueController::class, 'index'])->name('queue.index');
    Route::patch('/orders/{order}/complete', [OrderQueueController::class, 'complete'])->name('queue.complete');
    Route::patch('/orders/{order}/process', [OrderQueueController::class, 'process'])->name('queue.process');

    Route::get('/my-reports', [BaristaReportController::class, 'index'])->name('my-reports');

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');

    Route::post('/stock-reports', [\App\Http\Controllers\Pos\StockReportController::class, 'store'])->name('stock-reports.store');

    Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
    Route::post('/shifts/open', [ShiftController::class, 'open'])->name('shifts.open');
    Route::post('/shifts/close', [ShiftController::class, 'close'])->name('shifts.close');

    Route::post('/cash-expenses', [CashExpenseController::class, 'store'])->name('cash-expenses.store');
    Route::delete('/cash-expenses/{cashExpense}', [CashExpenseController::class, 'destroy'])->name('cash-expenses.destroy');

    // Stock Opname
    Route::get('/stock-opname', [PosStockOpnameController::class, 'index'])->name('stock-opname.index');
    Route::get('/stock-opname/create', [PosStockOpnameController::class, 'create'])->name('stock-opname.create');
    Route::post('/stock-opname', [PosStockOpnameController::class, 'store'])->name('stock-opname.store');
});
