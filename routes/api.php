<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use App\Http\Controllers\SystemLogController;
use App\Http\Controllers\Support\Messages;
use App\Http\Controllers\ClientGatewayTokenController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Auth;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientStatementController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyGatewayController;
use App\Http\Controllers\CompanyLedgerController;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\ConnectedAccountController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\GroupSettingController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ImportJsonController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\MigrationController;
use App\Http\Controllers\OneTimeTokenController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentTermController;
use App\Http\Controllers\PingController;
use App\Http\Controllers\PostMarkController;
use App\Http\Controllers\PreviewController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\RecurringInvoiceController;
use App\Http\Controllers\RecurringQuoteController;
use App\Http\Controllers\SchedulerController;
use App\Http\Controllers\SelfUpdateController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\SubdomainController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskStatusController;
use App\Http\Controllers\TaxRateController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\WebCronController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::middleware('api_secret_check')->group(function () {
    Route::post('api/v1/signup', [AccountController::class, 'store'])->name('signup.submit');
    Route::post('api/v1/oauth_login', [Auth\LoginController::class, 'oauthApiLogin']);
});

Route::middleware('api_secret_check', 'email_db')->group(['middleware' => ['api_secret_check','email_db']], function () {
    Route::post('api/v1/login', [Auth\LoginController::class, 'apiLogin'])->name('login.submit');
    Route::post('api/v1/reset_password', [Auth\ForgotPasswordController::class, 'sendResetLinkEmail']);
});

Route::middleware('api_db', 'token_auth', 'locale')->prefix('api/v1')->name('api.')->group(function () {
    Route::post('check_subdomain', [SubdomainController::class, 'index'])->name('check_subdomain');
    Route::get('ping', [PingController::class, 'index'])->name('ping');
    Route::get('health_check', [PingController::class, 'health'])->name('health_check');

    Route::get('activities', [ActivityController::class, 'index']);
    Route::get('activities/download_entity/{activity}', [ActivityController::class, 'downloadHistoricalEntity']);

    Route::post('claim_license', [LicenseController::class, 'index'])->name('license.index');

    Route::resource('clients', ClientController::class); // name = (clients. index / create / show / update / destroy / edit
    Route::put('clients/{client}/adjust_ledger', [ClientController::class, 'adjustLedger'])->name('clients.adjust_ledger');
    Route::put('clients/{client}/upload', [ClientController::class, 'upload'])->name('clients.upload');
    Route::post('clients/bulk', [ClientController::class, 'bulk'])->name('clients.bulk');

    Route::resource('client_gateway_tokens', ClientGatewayTokenController::class);
    
    Route::post('connected_account', [ConnectedAccountController::class, 'index']);
    Route::post('connected_account/gmail', [ConnectedAccountController::class, 'handleGmailOauth']);

    Route::resource('client_statement', [ClientStatementController::class, 'statement']); // name = (client_statement. index / create / show / update / destroy / edit

    Route::post('companies/purge/{company}', [MigrationController::class, 'purgeCompany'])->middleware('password_protected');
    Route::post('companies/purge_save_settings/{company}', [MigrationController::class, 'purgeCompanySaveSettings'])->middleware('password_protected');
    Route::resource('companies', CompanyController::class); // name = (companies. index / create / show / update / destroy / edit
    Route::put('companies/{company}/upload', [CompanyController::class, 'upload']);

    Route::get('company_ledger', [CompanyLedgerController::class, 'index'])->name('company_ledger.index');

    Route::resource('company_gateways', CompanyGatewayController::class);
    Route::post('company_gateways/bulk', [CompanyGatewayController::class, 'bulk'])->name('company_gateways.bulk');

    Route::put('company_users/{user}', [CompanyUserController::class, 'update']);

    Route::resource('credits', CreditController::class); // name = (credits. index / create / show / update / destroy / edit
    Route::put('credits/{credit}/upload', [CreditController::class, 'upload'])->name('credits.upload');
    Route::get('credits/{credit}/{action}', [CreditController::class, 'action'])->name('credits.action');
    Route::post('credits/bulk', [CreditController::class, 'bulk'])->name('credits.bulk');

    Route::resource('designs', DesignController::class); // name = (payments. index / create / show / update / destroy / edit
    Route::post('designs/bulk', [DesignController::class, 'bulk'])->name('designs.bulk');


    Route::resource('documents', DocumentController::class); // name = (documents. index / create / show / update / destroy / edit
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::post('documents/bulk', [DocumentController::class, 'bulk'])->name('documents.bulk');

    Route::post('emails', [EmailController::class, 'send'])->name('email.send')->middleware('user_verified');

    Route::resource('expenses', ExpenseController::class); // name = (expenses. index / create / show / update / destroy / edit
    Route::put('expenses/{expense}/upload', [ExpenseController::class, 'upload']);
    Route::post('expenses/bulk', [ExpenseController::class, 'bulk'])->name('expenses.bulk');

    Route::post('export', [ExportController::class, 'index'])->name('export.index');

    Route::resource('expense_categories', ExpenseCategoryController::class); // name = (expense_categories. index / create / show / update / destroy / edit
    Route::post('expense_categories/bulk', [ExpenseCategoryController::class, 'bulk'])->name('expense_categories.bulk');

    Route::resource('group_settings', GroupSettingController::class);
    Route::post('group_settings/bulk', [GroupSettingController::class, 'bulk']);

    Route::post('import', [ImportController::class, 'import'])->name('import.import');
    Route::post('import_json', [ImportJsonController::class, 'import'])->name('import.import_json');
    Route::post('preimport', [ImportController::class, 'preimport'])->name('import.preimport');

    Route::resource('invoices', InvoiceController::class); // name = (invoices. index / create / show / update / destroy / edit
    Route::get('invoices/{invoice}/delivery_note', [InvoiceController::class, 'deliveryNote'])->name('invoices.delivery_note');
    Route::get('invoices/{invoice}/{action}', [InvoiceController::class, 'action'])->name('invoices.action');
    Route::put('invoices/{invoice}/upload', [InvoiceController::class, 'upload'])->name('invoices.upload');
    Route::get('invoice/{invitation_key}/download', [InvoiceController::class, 'downloadPdf'])->name('invoices.downloadPdf');
    Route::post('invoices/bulk', [InvoiceController::class, 'bulk'])->name('invoices.bulk');
    
    Route::post('logout', [LogoutController::class, 'index'])->name('logout');

    Route::post('migrate', [MigrationController::class, 'index'])->name('migrate.start');

    Route::post('migration/purge/{company}', [MigrationController::class, 'purgeCompany'])->middleware('password_protected');
    Route::post('migration/purge_save_settings/{company}', [MigrationController::class, 'purgeCompanySaveSettings'])->middleware('password_protected');
    Route::post('migration/start', [MigrationController::class, 'startMigration']);

    Route::post('one_time_token', [OneTimeTokenController::class, 'create']);

    Route::resource('payments', PaymentController::class); // name = (payments. index / create / show / update / destroy / edit
    Route::post('payments/refund', [PaymentController::class, 'refund'])->name('payments.refund');
    Route::post('payments/bulk', [PaymentController::class, 'bulk'])->name('payments.bulk');
    Route::put('payments/{payment}/upload', [PaymentController::class, 'upload']);

    Route::resource('payment_terms', PaymentTermController::class); // name = (payments. index / create / show / update / destroy / edit
    Route::post('payment_terms/bulk', [PaymentTermController::class, 'bulk'])->name('payment_terms.bulk');

    Route::post('preview', [PreviewController::class, 'show'])->name('preview.show');

    Route::resource('products', ProductController::class); // name = (products. index / create / show / update / destroy / edit
    Route::post('products/bulk', [ProductController::class, 'bulk'])->name('products.bulk');
    Route::put('products/{product}/upload', [ProductController::class, 'upload']);

    Route::resource('projects', ProjectController::class); // name = (projects. index / create / show / update / destroy / edit
    Route::post('projects/bulk', [ProjectController::class, 'bulk'])->name('projects.bulk');
    Route::put('projects/{project}/upload', [ProjectController::class, 'upload'])->name('projects.upload');

    Route::resource('quotes', QuoteController::class); // name = (quotes. index / create / show / update / destroy / edit
    Route::get('quotes/{quote}/{action}', [QuoteController::class, 'action'])->name('quotes.action');
    Route::post('quotes/bulk', [QuoteController::class, 'bulk'])->name('quotes.bulk');
    Route::put('quotes/{quote}/upload', [QuoteController::class, 'upload']);

    Route::resource('recurring_invoices', RecurringInvoiceController::class); // name = (recurring_invoices. index / create / show / update / destroy / edit
    Route::post('recurring_invoices/bulk', [RecurringInvoiceController::class, 'bulk'])->name('recurring_invoices.bulk');
    Route::put('recurring_invoices/{recurring_invoice}/upload', [RecurringInvoiceController::class, 'upload']);
    Route::resource('recurring_quotes', RecurringQuoteController::class); // name = (recurring_invoices. index / create / show / update / destroy / edit

    Route::post('recurring_quotes/bulk', [RecurringQuoteController::class, 'bulk'])->name('recurring_quotes.bulk');

    Route::post('refresh', [Auth\LoginController::class, 'refresh']);

    Route::get('scheduler', [SchedulerController::class, 'index']);
    Route::post('support/messages/send', Support\Messages\SendingController::class);

    Route::post('self-update', [SelfUpdateController::class, 'update'])->middleware('password_protected');
    Route::post('self-update/check_version', [SelfUpdateController::class, 'checkVersion']);

    Route::resource('system_logs', SystemLogController::class);

    Route::resource('tasks', TaskController::class); // name = (tasks. index / create / show / update / destroy / edit
    Route::post('tasks/bulk', [TaskController::class, 'bulk'])->name('tasks.bulk');
    Route::put('tasks/{task}/upload', [TaskController::class, 'upload']);
    Route::post('tasks/sort', [TaskController::class, 'sort']);

    Route::resource('task_statuses', TaskStatusController::class); // name = (task_statuses. index / create / show / update / destroy / edit
    Route::post('task_statuses/bulk', [TaskStatusController::class, 'bulk'])->name('task_statuses.bulk');

    Route::resource('tax_rates', TaxRateController::class); // name = (tax_rates. index / create / show / update / destroy / edit
    Route::post('tax_rates/bulk', [TaxRateController::class, 'bulk'])->name('tax_rates.bulk');

    Route::post('templates', [TemplateController::class, 'show'])->name('templates.show');

    Route::resource('tokens', TokenController::class)->middleware('password_protected'); // name = (tokens. index / create / show / update / destroy / edit
    Route::post('tokens/bulk', [TokenController::class, 'bulk'])->name('tokens.bulk')->middleware('password_protected');

    Route::get('settings/enable_two_factor', [TwoFactorController::class, 'setupTwoFactor']);
    Route::post('settings/enable_two_factor', [TwoFactorController::class, 'enableTwoFactor']);
    Route::post('settings/disable_two_factor', [TwoFactorController::class, 'disableTwoFactor']);

    Route::resource('vendors', VendorController::class); // name = (vendors. index / create / show / update / destroy / edit
    Route::post('vendors/bulk', [VendorController::class, 'bulk'])->name('vendors.bulk');
    Route::put('vendors/{vendor}/upload', [VendorController::class, 'upload']);

    Route::get('users', [UserController::class, 'index']);
    Route::put('users/{user}', [UserController::class, 'update'])->middleware('password_protected');
    Route::post('users', [UserController::class, 'store'])->middleware('password_protected');
    //Route::post('users/{user}/attach_to_company', [UserController::class, 'attach'])->middleware('password_protected');
    Route::delete('users/{user}/detach_from_company', [UserController::class, 'detach'])->middleware('password_protected');

    Route::post('users/bulk', [UserController::class, 'bulk'])->name('users.bulk')->middleware('password_protected');
    Route::post('/users/{user}/invite', [UserController::class, 'invite'])->middleware('password_protected');
    Route::post('/user/{user}/reconfirm', [UserController::class, 'reconfirm']);

    Route::resource('webhooks', WebhookController::class);
    Route::post('webhooks/bulk', [WebhookController::class, 'bulk'])->name('webhooks.bulk');

    /*Subscription and Webhook routes */
    // Route::post('hooks', [SubscriptionController::class, 'subscribe'])->name('hooks.subscribe');
    // Route::delete('hooks/{subscription_id}', [SubscriptionController::class, 'unsubscribe'])->name('hooks.unsubscribe');

    Route::post('stripe/update_payment_methods', [StripeController::class, 'update'])->middleware('password_protected')->name('stripe.update');
    Route::post('stripe/import_customers', [StripeController::class, 'import'])->middleware('password_protected')->name('stripe.import');

    Route::resource('subscriptions', SubscriptionController::class);
    Route::post('subscriptions/bulk', [SubscriptionController::class, 'bulk'])->name('subscriptions.bulk');
});

Route::match(['get', 'post'], 'payment_webhook/{company_key}/{company_gateway_id}', 'PaymentWebhookController')
    ->middleware(['guest'])
    ->name('payment_webhook');

Route::post('api/v1/postmark_webhook', [PostMarkController::class, 'webhook']);
Route::get('token_hash_router', [OneTimeTokenController::class, 'router']);
Route::get('webcron', [WebCronController::class, 'index']);

Route::fallback([BaseController::class, 'notFound']);
