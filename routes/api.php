<?php

use App\Http\Controllers\BenefitsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/**1. Beneficios ordenados por años. */

Route::get('/benefits/by-year', [BenefitsController::class, 'getByYear']);

/**2. Monto total por año. */
Route::get('/benefits/total-amount-per-year', [BenefitsController::class, 'getTotalAmountPerYear']);

/**3. Número de beneficios por año. */
Route::get('/benefits/count-per-year', [BenefitsController::class, 'getCountPerYear']);

/**4. Filtrar solo los beneficios que cumplan los montos máximos y mínimos. */
Route::get('/benefits/filter-by-amount-range', [BenefitsController::class, 'filterByAmountRange']);

/**5. Cada beneficio debe traer su ficha. */
Route::get('/benefits/with-cards', [BenefitsController::class, 'getBenefitsWithCards']);

/**6. Se debe ordenar por año, de mayor a menor. */
Route::get('/benefits/by-year-asc-to-desc', [BenefitsController::class, 'getByYearAscToDesc']);

/**
formato final 
 */
Route::get('/benefits', [BenefitsController::class, 'getBenefitsInExpectedFormat']);
