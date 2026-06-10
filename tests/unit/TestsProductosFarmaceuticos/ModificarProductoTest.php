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

final class ModificarProductoTest extends CIUnitTestCase
{
    // Creacion de un conjunto de datos validos

    private function datosValidos(): array
    {
        return [
            'id_medicamento'       => 1,
            'id_tipo_producto'     => 2,
            'id_medida_producto'   => 3,
            'dosis_producto'       => 500.0,
            'descripcion_producto' => null,
        ];
    }

    // Creacion del servicio con todos los modelos necesarios
    private function crearServicio(
        ProductoFarmaceuticoModel $productoModel,
        ?MedicamentoService $medicamentoService = null
    ): ProductoFarmaceuticoService {
        return new ProductoFarmaceuticoService(
            $productoModel,
            $this->createMock(TipoProductoModel::class),
            $this->createMock(MedidaProductoModel::class),
            $this->createMock(MedicamentoModel::class),
            $medicamentoService ?? $this->createMock(MedicamentoService::class),
        );
    }

    // Mock completo de un producto existente en BD, con todos los objetos necesarios
    private function crearProductoMock(
        int $idMedicamento = 1,
        int $idTipo = 2,
        int $idMedida = 3,
        float $dosis = 500.0,
        ?string $descripcion = null,
        int $idProducto = 1,
        bool $activo = true
    ): ProductoFarmaceutico {
        $medicamento = $this->createMock(Medicamento::class);
        $medicamento->method('obtenerID')->willReturn($idMedicamento);

        $tipo = $this->createMock(TipoProducto::class);
        $tipo->method('obtenerID')->willReturn($idTipo);

        $medida = $this->createMock(MedidaProducto::class);
        $medida->method('obtenerID')->willReturn($idMedida);

        $producto = $this->createMock(ProductoFarmaceutico::class);
        $producto->method('obtenerID')->willReturn($idProducto);
        $producto->method('obtenerActivo')->willReturn($activo);
        $producto->method('obtenerMedicamento')->willReturn($medicamento);
        $producto->method('obtenerTipo')->willReturn($tipo);
        $producto->method('obtenerUnidadMedida')->willReturn($medida);
        $producto->method('obtenerDosis')->willReturn($dosis);
        $producto->method('obtenerDescripcion')->willReturn($descripcion);

        return $producto;
    }

    // Mockeo del modelo para una modifiacion exitosa
    private function modeloModificacionExitosa(): ProductoFarmaceuticoModel
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);
        $model->method('productoFarmaceuticoUnico')->willReturn(false);
        $model->method('modificar')->willReturn(true);
        return $model;
    }


    // **** PRODUCTO NO EXISTE ****

    public function test_ModificarProductoInexistente()
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);
        $model->method('buscarProductoPorID')->willReturn(null);

        $service = $this->crearServicio($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->modificarProductoFarmaceutico(999, $this->datosValidos());
    }

    // **** SIN CAMBIOS ****

    public function test_SinCambios()
    {
        $model = $this->modeloModificacionExitosa();
        // El producto en BD tiene exactamente los mismos datos que datosValidos()
        $model->method('buscarProductoPorID')->willReturn($this->crearProductoMock());

        $service = $this->crearServicio($model);

        // Mismos datos = false porque no hubo cambios
        $this->assertFalse($service->modificarProductoFarmaceutico(1, $this->datosValidos()));
    }

    // Verifica que modificar no se llama cuando no hay cambios
    public function test_ModificarNoInvocadoSiNohuboCambios()
    {
        $model = $this->modeloModificacionExitosa();
        $model->method('buscarProductoPorID')->willReturn($this->crearProductoMock());
        $model->expects($this->never())->method('modificar');

        $service = $this->crearServicio($model);
        $service->modificarProductoFarmaceutico(1, $this->datosValidos());
    }

    public function test_SoloCambiaDosisDetectaCambio()
    {
        $model = $this->modeloModificacionExitosa();
        // BD tiene dosis 500, se envía 250
        $model->method('buscarProductoPorID')->willReturn($this->crearProductoMock(dosis: 500.0));

        $service = $this->crearServicio($model);

        $this->assertTrue($service->modificarProductoFarmaceutico(
            1,
            array_merge($this->datosValidos(), ['dosis_producto' => 250.0])
        ));
    }

    public function test_SoloCambiaDescripcionDetectaCambio()
    {
        $model = $this->modeloModificacionExitosa();
        $model->method('buscarProductoPorID')->willReturn($this->crearProductoMock(descripcion: null));

        $service = $this->crearServicio($model);

        $this->assertTrue($service->modificarProductoFarmaceutico(
            1,
            array_merge($this->datosValidos(), ['descripcion_producto' => 'Comprimido recubierto'])
        ));
    }


    // **** VALIDACIÓN DE DATOS ****

    public function test_DosisNegativa()
    {
        $model = $this->modeloModificacionExitosa();
        $model->method('buscarProductoPorID')->willReturn($this->crearProductoMock(dosis: 500.0));

        $service = $this->crearServicio($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->modificarProductoFarmaceutico(1, array_merge($this->datosValidos(), ['dosis_producto' => -1.0]));
    }

    public function test_DosisCero()
    {
        $model = $this->modeloModificacionExitosa();
        $model->method('buscarProductoPorID')->willReturn($this->crearProductoMock(dosis: 500.0));

        $service = $this->crearServicio($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->modificarProductoFarmaceutico(1, array_merge($this->datosValidos(), ['dosis_producto' => 0.0]));
    }

    public function test_DosisSuperaElMaximo()
    {
        $model = $this->modeloModificacionExitosa();
        $model->method('buscarProductoPorID')->willReturn($this->crearProductoMock(dosis: 500.0));

        $service = $this->crearServicio($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->modificarProductoFarmaceutico(1, array_merge($this->datosValidos(), ['dosis_producto' => 3001.0]));
    }

    public function test_DescripcionConCaracterInvalido()
    {
        $model = $this->modeloModificacionExitosa();
        $model->method('buscarProductoPorID')->willReturn($this->crearProductoMock(dosis: 500.0));

        $service = $this->crearServicio($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->modificarProductoFarmaceutico(1, array_merge($this->datosValidos(), ['descripcion_producto' => 'Invalido#']));
    }

    public function test_IDTipoProductoCero()
    {
        $model = $this->modeloModificacionExitosa();
        $model->method('buscarProductoPorID')->willReturn($this->crearProductoMock());

        $service = $this->crearServicio($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->modificarProductoFarmaceutico(1, array_merge($this->datosValidos(), ['id_tipo_producto' => 0]));
    }

    public function test_IDMedidaProductoNegativo()
    {
        $model = $this->modeloModificacionExitosa();
        $model->method('buscarProductoPorID')->willReturn($this->crearProductoMock());

        $service = $this->crearServicio($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->modificarProductoFarmaceutico(1, array_merge($this->datosValidos(), ['id_medida_producto' => -1]));
    }


    // **** PRODUCTO DUPLICADO ****

    public function test_CombinacionDuplicada()
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);
        // El producto existe pero con dosis distinta
        $model->method('buscarProductoPorID')->willReturn($this->crearProductoMock(dosis: 500.0));
        // La nueva combinacion ya existe para otro producto
        $model->method('productoFarmaceuticoUnico')->willReturn(true);

        $service = $this->crearServicio($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->modificarProductoFarmaceutico(
            1,
            array_merge($this->datosValidos(), ['dosis_producto' => 250.0])
        );
    }


    // **** MODIFICACIÓN EXITOSA ****

    public function test_ModificacionExitosa()
    {
        $model = $this->modeloModificacionExitosa();
        $model->method('buscarProductoPorID')->willReturn($this->crearProductoMock(dosis: 500.0));

        $service = $this->crearServicio($model);

        $this->assertTrue($service->modificarProductoFarmaceutico(
            1,
            array_merge($this->datosValidos(), ['dosis_producto' => 250.0])
        ));
    }

    // Verifica que modificar se llama con el ID correcto
    public function test_ModificarSeLlamaConElIDCorrecto()
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);
        $model->method('buscarProductoPorID')->willReturn($this->crearProductoMock(dosis: 500.0));
        $model->method('productoFarmaceuticoUnico')->willReturn(false);

        $model->expects($this->once())
            ->method('modificar')
            ->with(7, $this->anything())
            ->willReturn(true);

        $service = $this->crearServicio($model);

        $service->modificarProductoFarmaceutico(
            7,
            array_merge($this->datosValidos(), ['dosis_producto' => 250.0])
        );
    }

    // **** OTRAS PRUEBAS ****

    //modificar devuelve false
    public function test_FallaBDAModificarFalse()
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);
        $model->method('buscarProductoPorID')->willReturn($this->crearProductoMock(dosis: 500.0));
        $model->method('productoFarmaceuticoUnico')->willReturn(false);
        $model->method('modificar')->willReturn(false); // BD falla

        $service = $this->crearServicio($model);

        $result = $service->modificarProductoFarmaceutico(
            1,
            array_merge($this->datosValidos(), ['dosis_producto' => 250.0])
        );
        $this->assertFalse($result);
    }


    // IDS para producto invalidos

    public function test_IDProductoCeroLanzaExcepcion()
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);
        $model->method('buscarProductoPorID')->willReturn(null);

        $service = $this->crearServicio($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->modificarProductoFarmaceutico(0, $this->datosValidos());
    }

    public function test_IDProductoNegativoLanzaExcepcion()
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);
        $model->method('buscarProductoPorID')->willReturn(null);

        $service = $this->crearServicio($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->modificarProductoFarmaceutico(-5, $this->datosValidos());
    }
}