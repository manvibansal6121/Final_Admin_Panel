<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ContentPageController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\StaffController;
use App\Http\Middleware\CheckPermissionMiddleware;
use App\Http\Controllers\BackupController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('unauthorized', function () {
    return response()->json([
        'message' => 'Please Provide Details',
    ], 401);
})->name('login');

// ADMIN
Route::middleware('auth:sanctum', 'admin-profile')->group(function () {
    Route::middleware(CheckPermissionMiddleware::class . ':' . 'Dashboard')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboard']);
    });

    // Users
    Route::middleware(CheckPermissionMiddleware::class . ':' . 'Users')->group(function () {
        Route::get('/list-users', [UserController::class, 'listUser']);
        Route::post('/add-users', [UserController::class, 'addUser']);
        Route::get('/view-users/{id}', [UserController::class, 'viewUser']);
        Route::put('/block-status-update/{id}', [UserController::class, 'block']);
        Route::put('/active-status-update/{id}', [UserController::class, 'active']);
        Route::delete('/delete-users/{id}', [UserController::class, 'destroy']);
        Route::get('/export-users',[UserController::class, 'userExport']);
    });

    // Products
    Route::middleware(CheckPermissionMiddleware::class . ':' . 'Products')->group(function () {
        Route::post('/add-products', [ProductController::class, 'addProduct']);
        Route::get('/list-products', [ProductController::class, 'view']);
        Route::get('/view-products/{id}', [ProductController::class, 'show']);
        Route::put('/visibility/{id}', [ProductController::class, 'visibility']);
        Route::put('/update-products/{id}', [ProductController::class, 'updateProduct']);
        Route::get('/export-products',[ProductController::class, 'productExport']);
        Route::post('/upload-product-image/{id}',[ProductController::class, 'UploadProductImage']);
    });

    // Content Page
    Route::middleware(CheckPermissionMiddleware::class . ':' . 'Content Page')->group(function () {
        Route::post('/add-contentpage', [ContentPageController::class, 'addPage']);
        Route::get('/listing-contentpage', [ContentPageController::class, 'view']);
        Route::get('/view-contentpage/{id}', [ContentPageController::class, 'show']);
        Route::put('/update-contentpage/{id}', [ContentPageController::class, 'update']);
    });

    // FAQs
    Route::middleware(CheckPermissionMiddleware::class . ':' . 'FAQ')->group(function () {
        Route::post('/add-faq', [FAQController::class, 'addFaq']);
        Route::get('/list-faq', [FAQController::class, 'view']);
        Route::put('/update-faq/{id}', [FAQController::class, 'update']);
    });

    // Contact
    Route::middleware(CheckPermissionMiddleware::class . ':' . 'Contact')->group(function () {
        Route::get('/view-contact', [ContactController::class, 'view']);
        Route::put('/change-status/{id}', [ContactController::class, 'changeStatus']);
        Route::delete('/delete-contact/{id}', [ContactController::class, 'destroy']);
        Route::get('/export-contact',[ContactController::class, 'contactExport']);
    });

    // Notifications
    Route::middleware(CheckPermissionMiddleware::class . ':' . 'Notification')->group(function () {
        Route::post('/send-mail', [NotificationController::class, 'sendMail']);
    });

    // Staff
    Route::middleware(CheckPermissionMiddleware::class . ':' . 'Staff')->group(function () {
        Route::post('/add-staff', [StaffController::class, 'addStaff']);
        Route::get('/list-staff', [StaffController::class, 'list']);
        Route::get('/view-staff/{id}', [StaffController::class, 'view']);
        Route::put('/update-staff/{id}', [StaffController::class, 'updateStaff']);
        Route::put('/block-staff/{id}', [StaffController::class, 'blockStaff']);
        Route::delete('/delete-staff/{id}', [StaffController::class, 'destroy']);
    });

    // DB Backup
    Route::middleware(CheckPermissionMiddleware::class. ':' . 'Backup')->group(function(){
        Route::get('/backup',[BackupController::class, 'createBackup']);
    });
    
    // Profile
    Route::middleware(CheckPermissionMiddleware::class . ':' . 'Profile')->group(function () {
        Route::get('/view-profile', [AdminAuthController::class, 'viewProfile']);
        Route::put('/update-profile', [AdminAuthController::class, 'updateProfile']);
        Route::put('/update-password', [AdminAuthController::class, 'changePassword']);
    });

    //Logout
    Route::post('/logout', [AdminAuthController::class, 'logout']);
});
// Admin login route
Route::post('/login', [AdminAuthController::class, 'login']);



//USER
//User Signup
Route::post('/signup',[UserController::class,'signup']);
//Verify Email
Route::post('/verify-email',[UserController::class,'verifyEmail']);
//Resend OTP
Route::post('/resend-otp',[UserController::class,'resendOTP']);
//Login User
Route::post('login-user',[UserController::class,'loginUser']);
//Authorized User 
Route::middleware('auth:sanctum','user-profile')->group(function(){
    Route::get('/view-user-profile',[UserController::class,'viewUserProfile']);
    
    Route::post('/logout-user',[UserController::class,'logoutUser']);
});


//Upload Image
Route::post('upload-image',[ImageUploadController::class,'imageUpload']);