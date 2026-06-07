<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\ProductoFarmaceuticoService;
use App\Models\ProductoFarmaceuticoModel;
use App\Models\TipoProductoModel;
use App\Models\MedidaProductoModel;
use App\Models\MedicamentoModel;
use App\Services\MedicamentoService;
use App\Entities\ProductoFarmaceutico;
use App\Entities\Medicamento;
use App\Entities\TipoProducto;
use App\Entities\MedidaProducto;

final class EliminarConMedicamentoTest extends CIUnitTestCase{
    
    public function test_EliminarConMedicamentoCorrectamente()
    {
        $productoModel = $this->createMock(ProductoFarmaceuticoModel::class);

        $medicamentoService = $this->createMock(MedicamentoService::class);

        $medicamentoService->expects($this->once())
            ->method('eliminarMedicamento')
            ->with(5)
            ->willReturn(true);

        $productoModel->expects($this->once())
            ->method('desactivarPorMedicamento')
            ->with(5);

        $service = new ProductoFarmaceuticoService(
            $productoModel,
            null,
            null,
            null,
            $medicamentoService
        );

        $service->eliminarConMedicamento(5);

        $this->assertTrue(true); // evita warning de test sin assertions
    }

    public function test_EliminarConMedicamentoUsaElIDCorrecto()
    {
        $productoModel = $this->createMock(ProductoFarmaceuticoModel::class);

        $medicamentoService = $this->createMock(MedicamentoService::class);

        $medicamentoService->expects($this->once())
            ->method('eliminarMedicamento')
            ->with(99)
            ->willReturn(true);

        $productoModel->expects($this->once())
            ->method('desactivarPorMedicamento')
            ->with(99);

        $service = new ProductoFarmaceuticoService(
            $productoModel,
            null,
            null,
            null,
            $medicamentoService
        );

        $service->eliminarConMedicamento(99);
    }

    public function test_NoDesactivaProductosSiFallaEliminarMedicamento()
    {
        $productoModel = $this->createMock(ProductoFarmaceuticoModel::class);

        $medicamentoService = $this->createMock(MedicamentoService::class);

        $medicamentoService->expects($this->once())
            ->method('eliminarMedicamento')
            ->with(5)
            ->willThrowException(
                new \InvalidArgumentException('Medicamento inexistente')
            );

        $productoModel->expects($this->never())
            ->method('desactivarPorMedicamento');

        $service = new ProductoFarmaceuticoService(
            $productoModel,
            null,
            null,
            null,
            $medicamentoService
        );

        $this->expectException(\InvalidArgumentException::class);

        $service->eliminarConMedicamento(5);
    }

    public function test_CadaMetodoSeInvocaUnaSolaVez()
    {
        $productoModel = $this->createMock(ProductoFarmaceuticoModel::class);

        $medicamentoService = $this->createMock(MedicamentoService::class);

        $medicamentoService->expects($this->once())
            ->method('eliminarMedicamento')
            ->with(1)
            ->willReturn(true);

        $productoModel->expects($this->once())
            ->method('desactivarPorMedicamento')
            ->with(1);

        $service = new ProductoFarmaceuticoService(
            $productoModel,
            null,
            null,
            null,
            $medicamentoService
        );

        $service->eliminarConMedicamento(1);
    }
}