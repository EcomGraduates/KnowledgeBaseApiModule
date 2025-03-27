<?php

// Settings routes (admin only)
Route::group(['middleware' => ['web', 'auth', 'roles'], 'roles' => ['admin'], 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\KnowledgeBaseApiModule\Http\Controllers'], function () {
    Route::get('/app-settings/knowledge-base-api', ['uses' => 'SettingsController@index'])->name('knowledgebase-api-module.settings');
    Route::post('/app-settings/knowledge-base-api', ['uses' => 'SettingsController@save'])->name('knowledgebase-api-module.settings.save');
    
    // Direct access route for debugging
    Route::get('/kb-api-settings', ['uses' => 'SettingsController@index'])->name('knowledgebase-api-module.direct-settings');
});

// API routes (protected by token middleware)
Route::group(['middleware' => ['knowledgebase.api.token'], 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\KnowledgeBaseApiModule\Http\Controllers'], function () {
    Route::get('/api/knowledgebase/{mailboxId}/categories', ['uses' => 'KnowledgeBaseApiController@get', 'laroute' => false])->name('knowledgebase.index');
    Route::get('/api/knowledgebase/{mailboxId}/categories/{categoryId}', ['uses' => 'KnowledgeBaseApiController@category', 'laroute' => false])->name('knowledgebase.category');
});
