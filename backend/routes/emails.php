
<?php

// routes/api.php
use App\Http\Controllers\Admin\EmailTemplateController;

Route::prefix('email-templates')->group(function () {
    // Get all templates (paginated)
    Route::get('/', [EmailTemplateController::class, 'index']);

    // Create new template
    Route::post('/', [EmailTemplateController::class, 'store']);

    // Get single template
    Route::get('/{id}', [EmailTemplateController::class, 'show']);

    // Update template
    Route::put('/{id}', [EmailTemplateController::class, 'update']);

    // Delete template
    Route::delete('/{id}', [EmailTemplateController::class, 'destroy']);

    // Get template variables
    Route::get('/{id}/variables', [EmailTemplateController::class, 'getVariables']);

    // Send email using template
    Route::post('/send', [EmailTemplateController::class, 'sendEmail']);

    // Send test email
    Route::post('/send-test', [EmailTemplateController::class, 'sendTestEmail']);

    // Initialize default templates
    Route::post('/initialize', [EmailTemplateController::class, 'initializeTemplates']);
});
