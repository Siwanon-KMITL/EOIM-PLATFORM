<?php

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\AnalyticsController;
use App\Controllers\AlertsController;
use App\Controllers\DashboardController;
use App\Controllers\BillingController;
use App\Controllers\DeviceController;
use App\Controllers\SettingsController;
use App\Controllers\UserController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Controllers\MonitoringController;

Router::get('/', [AuthController::class, 'loginForm']);
Router::get('/login', [AuthController::class, 'loginForm']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/forgot-password', [AuthController::class, 'forgotPasswordForm']);
Router::post('/forgot-password', [AuthController::class, 'sendForgotPassword']);
Router::get('/reset-password', [AuthController::class, 'resetPasswordForm']);
Router::post('/reset-password', [AuthController::class, 'resetPassword']);
Router::get('/register', [AuthController::class, 'registerForm']);
Router::post('/register', [AuthController::class, 'register']);
Router::get('/logout', [AuthController::class, 'logout'], [
    AuthMiddleware::class
]);

Router::get('/dashboard', [DashboardController::class, 'index'], [
    AuthMiddleware::class
]);

Router::get('/analytics', [AnalyticsController::class, 'index'], [
    AuthMiddleware::class
]);

Router::get('/alerts', [AlertsController::class, 'index'], [
    AuthMiddleware::class
]);

Router::get('/settings', [SettingsController::class, 'index'], [
    AuthMiddleware::class
]);

Router::get('/billing', [BillingController::class, 'index'], [
    AuthMiddleware::class
]);

Router::get('/devices', [DeviceController::class, 'index'], [
    AuthMiddleware::class
]);

Router::get('/devices/create', [DeviceController::class, 'create'], [
    AuthMiddleware::class
]);

Router::post('/devices/store', [DeviceController::class, 'store'], [
    AuthMiddleware::class
]);

Router::get('/devices/edit', [DeviceController::class, 'edit'], [
    AuthMiddleware::class
]);

Router::post('/devices/update', [DeviceController::class, 'update'], [
    AuthMiddleware::class
]);

Router::post('/devices/delete', [DeviceController::class, 'delete'], [
    AuthMiddleware::class,
    [RoleMiddleware::class, ['admin']]
]);

Router::get('/users', [UserController::class, 'index'], [
    AuthMiddleware::class,
    [RoleMiddleware::class, ['admin']]
]);
Router::get('/users/create', [UserController::class, 'create'], [
    AuthMiddleware::class,
    [RoleMiddleware::class, ['admin']]
]);
Router::post('/users/store', [UserController::class, 'store'], [
    AuthMiddleware::class,
    [RoleMiddleware::class, ['admin']]
]);
Router::get('/users/edit', [UserController::class, 'edit'], [
    AuthMiddleware::class,
    [RoleMiddleware::class, ['admin']]
]);
Router::post('/users/update', [UserController::class, 'update'], [
    AuthMiddleware::class,
    [RoleMiddleware::class, ['admin']]
]);
Router::post('/users/delete', [UserController::class, 'delete'], [
    AuthMiddleware::class,
    [RoleMiddleware::class, ['admin']]
]);

Router::get('/profile', [UserController::class, 'profile'], [
    AuthMiddleware::class
]);
Router::post('/profile/update', [UserController::class, 'updateProfile'], [
    AuthMiddleware::class
]);
Router::get('/profile/password', [UserController::class, 'passwordForm'], [
    AuthMiddleware::class
]);
Router::post('/profile/password', [UserController::class, 'updatePassword'], [
    AuthMiddleware::class
]);

Router::get('/monitoring/device', [MonitoringController::class, 'deviceDetail'], [
    AuthMiddleware::class
]);
