<?php

use App\Http\Controllers\Api\DeckController;
use App\Http\Controllers\Api\GameSessionController;
use App\Http\Controllers\Api\ProgressionController;
use App\Http\Controllers\Api\AchievementController;
use App\Http\Controllers\Api\MissionController;
use Illuminate\Support\Facades\Route;

Route::get('/decks', [DeckController::class, 'index']);
Route::get('/decks/{deck}/cards', [DeckController::class, 'cards'])->middleware('throttle:content-preload');

Route::prefix('/game-sessions')->group(function () {
    Route::post('/', [GameSessionController::class, 'store'])->middleware('throttle:gameplay');
    Route::get('/{gameSession}', [GameSessionController::class, 'show']);
    Route::post('/{gameSession}/submit-round', [GameSessionController::class, 'submitRound'])->middleware('throttle:gameplay');
    Route::post('/{gameSession}/finish', [GameSessionController::class, 'finish'])->middleware('throttle:gameplay');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/progression', [ProgressionController::class, 'show']);
    Route::get('/achievements/my', [AchievementController::class, 'mine']);
    Route::get('/missions/daily', [MissionController::class, 'daily']);
    Route::get('/missions/weekly', [MissionController::class, 'weekly']);
});

Route::get('/achievements', [AchievementController::class, 'index']);
