<?php

use App\Http\Controllers\Api\WhatsAppDebugController;
use App\Http\Controllers\Api\WhatsAppMessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// WhatsApp Routes (Public - no authentication)
Route::group(['prefix' => 'whatsapp'], function () {
    // Send text message
    Route::post('/send-text', [WhatsAppMessageController::class, 'sendTextMessage']);

    // Send image message
    Route::post('/send-image', [WhatsAppMessageController::class, 'sendImageMessage']);

    // Send template message
    Route::post('/send-template', [WhatsAppMessageController::class, 'sendTemplateMessage']);

    // Send document message
    Route::post('/send-document', [WhatsAppMessageController::class, 'sendDocumentMessage']);

    // Upload media
    Route::post('/upload-media', [WhatsAppMessageController::class, 'uploadMedia']);

    // Get templates list
    Route::get('/templates', [WhatsAppMessageController::class, 'getTemplates']);

    // Create template
    Route::post('/templates', [WhatsAppMessageController::class, 'createTemplate']);

    // Fetch media
    Route::get('/media-url', [WhatsAppMessageController::class, 'fetchMedia']);

    // Delete media
    Route::delete('/delete/media', [WhatsAppMessageController::class, 'deleteMedia']);

    // DEBUG ROUTES
    Route::get('/debug/config', [WhatsAppDebugController::class, 'checkConfig']);
    Route::get('/debug/validate-token', [WhatsAppDebugController::class, 'validateToken']);
    Route::get('/debug/phone-numbers', [WhatsAppDebugController::class, 'getPhoneNumbers']);
    Route::post('/debug/test-message', [WhatsAppDebugController::class, 'sendTestMessage']);
});
