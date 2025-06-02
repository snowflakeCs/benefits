<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class BenefitsController extends Controller
{
    /**
     *  beneficios ordenados por año
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByYear()
    {
        try {
            // Fetch data
            $benefitsResponse = Http::get('https://run.mocky.io/v3/8f75c4b5-ad90-49bb-bc52-f1fc0b4aad02');
            $filtersResponse = Http::get('https://run.mocky.io/v3/b0ddc735-cfc9-410e-9365-137e04e33fcf');
            $cardsResponse = Http::get('https://run.mocky.io/v3/4654cafa-58d8-4846-9256-79841b29a687');

            if (!$benefitsResponse->successful() || !$filtersResponse->successful() || !$cardsResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'error al obtener datos de las APIs'
                ], 500);
            }

            // Get data
            $benefitsData = $benefitsResponse->json('data');
            $filtersData = $filtersResponse->json('data');
            $cardsData = $cardsResponse->json('data');

            // collection
            $benefitsCollection = collect($benefitsData);
            
            // group por año
            $benefitsByYear = $benefitsCollection->groupBy(function ($benefit) {
                // Extrer año
                return substr($benefit['fecha'], 0, 4);
            })->sortKeysDesc(); // filtrar por año desc
            
            return response()->json([
                'success' => true,
                'data' => $benefitsByYear
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * monto total de beneficios por año
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTotalAmountPerYear()
    {
        try {
            $benefitsResponse = Http::get('https://run.mocky.io/v3/8f75c4b5-ad90-49bb-bc52-f1fc0b4aad02');

            if (!$benefitsResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'error al obtener datos de las APIs'
                ], 500);
            }

            $benefitsData = $benefitsResponse->json('data');
            $benefitsCollection = collect($benefitsData);
            
            // Group y calculo el monto total por año
            $totalAmountPerYear = $benefitsCollection
                ->groupBy(function ($benefit) {
                    return substr($benefit['fecha'], 0, 4);
                })
                ->map(function ($yearBenefits) {
                    return $yearBenefits->sum('monto');
                })
                ->sortKeysDesc(); // desc
            
            return response()->json([
                'success' => true,
                'data' => $totalAmountPerYear
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * conteo de beneficios por año
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCountPerYear()
    {
        try {
            $benefitsResponse = Http::get('https://run.mocky.io/v3/8f75c4b5-ad90-49bb-bc52-f1fc0b4aad02');

            if (!$benefitsResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'error al obtener datos de las APIs'
                ], 500);
            }

            $benefitsData = $benefitsResponse->json('data');
            $benefitsCollection = collect($benefitsData);
            
            $countPerYear = $benefitsCollection
                ->groupBy(function ($benefit) {
                    return substr($benefit['fecha'], 0, 4);
                })
                ->map(function ($yearBenefits) {
                    return $yearBenefits->count();
                })
                ->sortKeysDesc(); 
            
            return response()->json([
                'success' => true,
                'data' => $countPerYear
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Filtrar beneficios por rango de monto 
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterByAmountRange()
    {
        try {
            $benefitsResponse = Http::get('https://run.mocky.io/v3/8f75c4b5-ad90-49bb-bc52-f1fc0b4aad02');
            $filtersResponse = Http::get('https://run.mocky.io/v3/b0ddc735-cfc9-410e-9365-137e04e33fcf');

            if (!$benefitsResponse->successful() || !$filtersResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'error al obtener datos de las APIs'
                ], 500);
            }

            $benefitsData = $benefitsResponse->json('data');
            $filtersData = $filtersResponse->json('data');

            $benefitsCollection = collect($benefitsData);
            $filtersCollection = collect($filtersData);

            // filtrar beneficios por rango de monto
            $filteredBenefits = $benefitsCollection->filter(function ($benefit) use ($filtersCollection) {
                // encontrar el filtro correspondiente
                $filter = $filtersCollection->firstWhere('id_programa', $benefit['id_programa']);
                
                if ($filter) {
                    // Verificar si el monto del beneficio está dentro del rango mínimo y máximo
                    return $benefit['monto'] >= $filter['min'] && $benefit['monto'] <= $filter['max'];
                }
                
                return false; 
            });
            
            return response()->json([
                'success' => true,
                'data' => $filteredBenefits->values() 
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     *  beneficios con sus fichas
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBenefitsWithCards()
    {
        try {
            $benefitsResponse = Http::get('https://run.mocky.io/v3/8f75c4b5-ad90-49bb-bc52-f1fc0b4aad02');
            $filtersResponse = Http::get('https://run.mocky.io/v3/b0ddc735-cfc9-410e-9365-137e04e33fcf');
            $cardsResponse = Http::get('https://run.mocky.io/v3/4654cafa-58d8-4846-9256-79841b29a687');

            if (!$benefitsResponse->successful() || !$filtersResponse->successful() || !$cardsResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'error al obtener datos de las APIs'
                ], 500);
            }

            $benefitsData = $benefitsResponse->json('data');
            $filtersData = $filtersResponse->json('data');
            $cardsData = $cardsResponse->json('data');

            $benefitsCollection = collect($benefitsData);
            $filtersCollection = collect($filtersData);
            $cardsCollection = collect($cardsData);

            // mapeo de beneficios con sus fichas
            $benefitsWithCards = $benefitsCollection->map(function ($benefit) use ($filtersCollection, $cardsCollection) {
                $filter = $filtersCollection->firstWhere('id_programa', $benefit['id_programa']);
                
                if ($filter) {
                    // Encontrar la ficha correspondiente usando el ficha_id del filtro
                    $card = $cardsCollection->firstWhere('id', $filter['ficha_id']);
                    
                    if ($card) {
                        $benefit['ficha'] = $card;
                    }
                }
                
                return $benefit;
            });
            
            return response()->json([
                'success' => true,
                'data' => $benefitsWithCards->values() 
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * obtener beneficios ordenados por año de mayor a menor
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByYearAscToDesc()
    {
        try {
            $benefitsResponse = Http::get('https://run.mocky.io/v3/8f75c4b5-ad90-49bb-bc52-f1fc0b4aad02');

            // Check if request was successful
            if (!$benefitsResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'error al obtener datos de las APIs'
                ], 500);
            }

            $benefitsData = $benefitsResponse->json('data');
            $benefitsCollection = collect($benefitsData);
            
            $benefitsByYear = $benefitsCollection->groupBy(function ($benefit) {
                return substr($benefit['fecha'], 0, 4);
            })->sortKeys(); 
            
            return response()->json([
                'success' => true,
                'data' => $benefitsByYear
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error ' . $e->getMessage()
            ], 500);
        }
    }

     /**
     * obtener beneficios 2 formateado
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBenefitsInExpectedFormat()
    {
        try {
            $benefitsResponse = Http::get('https://run.mocky.io/v3/8f75c4b5-ad90-49bb-bc52-f1fc0b4aad02');
            $filtersResponse = Http::get('https://run.mocky.io/v3/b0ddc735-cfc9-410e-9365-137e04e33fcf');
            $cardsResponse = Http::get('https://run.mocky.io/v3/4654cafa-58d8-4846-9256-79841b29a687');

            // Check if all requests were successful
            if (!$benefitsResponse->successful() || !$filtersResponse->successful() || !$cardsResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'error al obtener datos de las APIs'
                ], 500);
            }

            $benefitsData = $benefitsResponse->json('data');
            $filtersData = $filtersResponse->json('data');
            $cardsData = $cardsResponse->json('data');

            $benefitsCollection = collect($benefitsData);
            $filtersCollection = collect($filtersData);
            $cardsCollection = collect($cardsData);

            // añadir ficha a cada beneficio
            $benefitsWithCards = $benefitsCollection->map(function ($benefit) use ($filtersCollection, $cardsCollection) {
                $year = substr($benefit['fecha'], 0, 4);
                $benefit['ano'] = $year;
                
                $benefit['view'] = true;

                // Encontrar la ficha correspondiente 
                $filter = $filtersCollection->firstWhere('id_programa', $benefit['id_programa']);
                
                if ($filter) {
                    // encontrar la ficha correspondiente
                    $card = $cardsCollection->firstWhere('id', $filter['ficha_id']);
                    
                    if ($card) {
                        $benefit['ficha'] = $card;
                    }
                }
                
                return $benefit;
            });
            
            $benefitsByYear = $benefitsWithCards->groupBy('ano');

            // formateo segun ejemplo 
            $formattedData = $benefitsByYear->map(function ($yearBenefits, $year) {
                return [
                    'year' => (int)$year,
                    'num' => $yearBenefits->count(),
                    'beneficios' => $yearBenefits->values()->all()
                ];
            })->values()->sortByDesc('year')->values();
            
            return response()->json([
                'code' => 200,
                'success' => true,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => 'Error ' . $e->getMessage()
            ], 500);
        }
    }

}