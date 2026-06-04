<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\ProductoFarmaceuticoService;
use App\Models\ProductoFarmaceuticoModel;
use App\Entities\ProductoFarmaceutico;

final class EliminarProductoTest extends CIUnitTestCase
{
    // creacion de un mockeo de producto
    private function crearEntidadMock(bool $activo = true,int $id = 1): ProductoFarmaceutico
    {
        $entity = $this->createMock(ProductoFarmaceutico::class);

        $entity->method('obtenerActivo')
            ->willReturn($activo);

        $entity->method('obtenerID')
            ->willReturn($id);

        return $entity;
    }

    // **** PRODUCTO NO EXISTE ****
    public function test_EliminarProductoInexistente()
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);

        $model->method('buscarProductoPorID')
            ->willReturn(null);

        $service = new ProductoFarmaceuticoService($model);

        $this->expectException(\InvalidArgumentException::class);

        $service->eliminarProducto(999);
    }

    // **** PRODUCTO INACTIVO ****

    public function test_EliminarProductoYaInactivo()
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);

        $model->method('buscarProductoPorID')
            ->willReturn(
                $this->crearEntidadMock(false, 1)
            );

        $service = new ProductoFarmaceuticoService($model);

        $this->expectException(\InvalidArgumentException::class);

        $service->eliminarProducto(1);
    }

    // **** ELIMINACION EXITOSA ****

    public function test_EliminarProductoActivo()
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);

        $model->method('buscarProductoPorID')
            ->willReturn(
                $this->crearEntidadMock(true, 1)
            );

        $model->method('desactivar')
            ->willReturn(true);

        $service = new ProductoFarmaceuticoService($model);

        $this->assertTrue(
            $service->eliminarProducto(1)
        );
    }


    // **** FALLA EN BD ****

    public function test_DesactivarDevuelveFalse()
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);

        $model->method('buscarProductoPorID')
            ->willReturn(
                $this->crearEntidadMock(true, 1)
            );

        $model->method('desactivar')
            ->willReturn(false);

        $service = new ProductoFarmaceuticoService($model);

        $this->assertFalse(
            $service->eliminarProducto(1)
        );
    }

    // **** IDS INVALIDOS ****

    public function test_IDCero()
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);

        $model->method('buscarProductoPorID')
            ->willReturn(null);

        $service = new ProductoFarmaceuticoService($model);

        $this->expectException(\InvalidArgumentException::class);

        $service->eliminarProducto(0);
    }

    public function test_IDNegativo()
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);

        $model->method('buscarProductoPorID')
            ->willReturn(null);

        $service = new ProductoFarmaceuticoService($model);

        $this->expectException(\InvalidArgumentException::class);

        $service->eliminarProducto(-10);
    }
}