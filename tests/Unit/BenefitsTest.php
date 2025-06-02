<?php

namespace Tests\Unit;

use App\Http\Controllers\BenefitsController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BenefitsTest extends TestCase
{
    protected $benefitsController;
    protected $mockBenefits;
    protected $mockFilters;
    protected $mockCards;

    protected function setUp(): void
    {
        parent::setUp();
        
        // mock data
        $this->mockBenefits = [
            'data' => [
                [
                    'id_programa' => 147,
                    'monto' => 40656,
                    'fecha_recepcion' => '09/11/2023',
                    'fecha' => '2023-11-09'
                ],
                [
                    'id_programa' => 148,
                    'monto' => 35000,
                    'fecha_recepcion' => '15/10/2023',
                    'fecha' => '2023-10-15'
                ],
                [
                    'id_programa' => 149,
                    'monto' => 25000,
                    'fecha_recepcion' => '20/12/2022',
                    'fecha' => '2022-12-20'
                ]
            ]
        ];
        
        $this->mockFilters = [
            'data' => [
                [
                    'id' => 1,
                    'id_programa' => 147,
                    'min' => 30000,
                    'max' => 50000,
                    'ficha_id' => 922
                ],
                [
                    'id' => 2,
                    'id_programa' => 148,
                    'min' => 20000,
                    'max' => 40000,
                    'ficha_id' => 923
                ],
                [
                    'id' => 3,
                    'id_programa' => 149,
                    'min' => 10000,
                    'max' => 30000,
                    'ficha_id' => 924
                ]
            ]
        ];
        
        $this->mockCards = [
            'data' => [
                [
                    'id' => 922,
                    'nombre' => 'Emprende',
                    'id_programa' => 147,
                    'url' => 'emprende',
                    'categoria' => 'trabajo',
                    'descripcion' => 'Fondos concursables para nuevos negocios'
                ],
                [
                    'id' => 923,
                    'nombre' => 'Capacitación',
                    'id_programa' => 148,
                    'url' => 'capacitacion',
                    'categoria' => 'educacion',
                    'descripcion' => 'Cursos de formación profesional'
                ],
                [
                    'id' => 924,
                    'nombre' => 'Vivienda',
                    'id_programa' => 149,
                    'url' => 'vivienda',
                    'categoria' => 'hogar',
                    'descripcion' => 'Subsidio para compra de vivienda'
                ]
            ]
        ];
        
        // Mock HTTP responses
        Http::fake([
            'https://run.mocky.io/v3/8f75c4b5-ad90-49bb-bc52-f1fc0b4aad02' => Http::response($this->mockBenefits),
            'https://run.mocky.io/v3/b0ddc735-cfc9-410e-9365-137e04e33fcf' => Http::response($this->mockFilters),
            'https://run.mocky.io/v3/4654cafa-58d8-4846-9256-79841b29a687' => Http::response($this->mockCards)
        ]);
        
        $this->benefitsController = new BenefitsController();
    }

    /** @test */
    public function it_can_get_benefits_by_year()
    {
        $response = $this->benefitsController->getByYear();
        $data = json_decode($response->getContent(), true);
        
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('2023', $data['data']);
        $this->assertArrayHasKey('2022', $data['data']);
        $this->assertCount(2, $data['data']['2023']);
        $this->assertCount(1, $data['data']['2022']);
    }
    
    /** @test */
    public function it_can_get_total_amount_per_year()
    {
        $response = $this->benefitsController->getTotalAmountPerYear();
        $data = json_decode($response->getContent(), true);
        
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('2023', $data['data']);
        $this->assertArrayHasKey('2022', $data['data']);
        $this->assertEquals(75656, $data['data']['2023']); 
        $this->assertEquals(25000, $data['data']['2022']);
    }
    
    /** @test */
    public function it_can_get_count_per_year()
    {
        $response = $this->benefitsController->getCountPerYear();
        $data = json_decode($response->getContent(), true);
        
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('2023', $data['data']);
        $this->assertArrayHasKey('2022', $data['data']);
        $this->assertEquals(2, $data['data']['2023']);
        $this->assertEquals(1, $data['data']['2022']);
    }
    
    /** @test */
    public function it_can_filter_benefits_by_amount_range()
    {
        $response = $this->benefitsController->filterByAmountRange();
        $data = json_decode($response->getContent(), true);
        
        $this->assertTrue($data['success']);
        $this->assertIsArray($data['data']);
        
        foreach ($data['data'] as $benefit) {
            $filter = collect($this->mockFilters['data'])->firstWhere('id_programa', $benefit['id_programa']);
            $this->assertGreaterThanOrEqual($filter['min'], $benefit['monto']);
            $this->assertLessThanOrEqual($filter['max'], $benefit['monto']);
        }
    }
    
    /** @test */
    public function it_can_get_benefits_with_cards()
    {
        $response = $this->benefitsController->getBenefitsWithCards();
        $data = json_decode($response->getContent(), true);
        
        $this->assertTrue($data['success']);
        $this->assertIsArray($data['data']);
        
        foreach ($data['data'] as $benefit) {
            $this->assertArrayHasKey('ficha', $benefit);
            $this->assertEquals($benefit['id_programa'], $benefit['ficha']['id_programa']);
        }
    }
    
    /** @test */
    public function it_can_get_benefits_by_year_ascending()
    {
        $response = $this->benefitsController->getByYearAscToDesc();
        $data = json_decode($response->getContent(), true);
        
        $this->assertTrue($data['success']);
        
        $years = array_keys($data['data']);
        $this->assertEquals(['2022', '2023'], $years);
    }
    
    /** @test */
    public function it_can_get_benefits_in_expected_format()
    {
        $response = $this->benefitsController->getBenefitsInExpectedFormat();
        $data = json_decode($response->getContent(), true);
        
        $this->assertEquals(200, $data['code']);
        $this->assertTrue($data['success']);
        $this->assertIsArray($data['data']);
        
        // estructura de la respuesta
        foreach ($data['data'] as $yearGroup) {
            $this->assertArrayHasKey('year', $yearGroup);
            $this->assertArrayHasKey('num', $yearGroup);
            $this->assertArrayHasKey('beneficios', $yearGroup);
            
            // beneficiarios por año
            foreach ($yearGroup['beneficios'] as $benefit) {
                $this->assertArrayHasKey('id_programa', $benefit);
                $this->assertArrayHasKey('monto', $benefit);
                $this->assertArrayHasKey('fecha', $benefit);
                $this->assertArrayHasKey('ano', $benefit);
                $this->assertArrayHasKey('view', $benefit);
                $this->assertArrayHasKey('ficha', $benefit);
                
                // año debe coincidir con año del grupo
                $this->assertEquals($yearGroup['year'], $benefit['ano']);
            }
            
            // revisar que el numero de beneficios coincida con el conteo
            $this->assertEquals(count($yearGroup['beneficios']), $yearGroup['num']);
        }
    }
}