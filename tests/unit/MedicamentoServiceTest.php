<?php

namespace Tests\Unit;
use CodeIgniter\Test\CIUnitTestCase;
use Config\App;
use Tests\Support\Libraries\ConfigReader;
use App\Services\MedicamentoService;

final class MedicamentoServiceTest extends CIUnitTestCase{
    // Se va a probar con espacios a ambos costados
    public function test_CrearMedicamento_NombreMuyCorto()
    {
        $service = new MedicamentoService();

        $this->expectException(\InvalidArgumentException::class);

        $service->crearMedicamento('ab');
    }
}