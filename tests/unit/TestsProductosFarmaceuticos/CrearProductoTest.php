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

final class CrearProductoTest extends CIUnitTestCase
{
    // creaciones de mockeos y datos validos

    // Datos validos
    private function datosValidos(): array
    {
        return [
            'id_medicamento'      => 1,
            'id_tipo_producto'    => 2,
            'id_medida_producto'  => 3,
            'dosis_producto'      => 500.0,
            'descripcion_producto'=> null,
        ];
    }

    // arma el service con los modelos necesarios
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

    // mockeo del modelo para el caso exitoso
    private function modeloNuevoProducto(int $idRetornado = 1): ProductoFarmaceuticoModel
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);
        $model->method('productoFarmaceuticoUnico')->willReturn(false);
        $model->method('buscarProductoExistente')->willReturn(null);
        $model->method('agregar')->willReturn($idRetornado);
        return $model;
    }

    // mockeo de una entidad producto
    private function crearProductoMock(bool $activo, int $id = 10): ProductoFarmaceutico
    {
        $producto = $this->createMock(ProductoFarmaceutico::class);
        $producto->method('obtenerActivo')->willReturn($activo);
        $producto->method('obtenerID')->willReturn($id);
        return $producto;
    }


    // **** CREACION EXITOSA ****
    public function test_CrearProductoNuevo()
    {
        $model = $this->modeloNuevoProducto(idRetornado: 1);
        $service = $this->crearServicio($model);

        $this->assertSame(1, $service->crearProducto($this->datosValidos()));
    }

    public function test_CrearProductoVerificacionIDCorrecto()
    {
        $model = $this->modeloNuevoProducto(idRetornado: 42);
        $service = $this->crearServicio($model);

        $this->assertSame(42, $service->crearProducto($this->datosValidos()));
    }

    // **** VALIDACIÓN DE DOSIS ****

    // dosis negativa
    public function test_DosisNegativa()
    {
        $service = $this->crearServicio($this->modeloNuevoProducto());

        $this->expectException(\InvalidArgumentException::class);
        $service->crearProducto(array_merge($this->datosValidos(), ['dosis_producto' => -1]));
    }

    // sin dosis
    public function test_DosisCero()
    {
        $service = $this->crearServicio($this->modeloNuevoProducto());

        $this->expectException(\InvalidArgumentException::class);
        $service->crearProducto(array_merge($this->datosValidos(), ['dosis_producto' => 0]));
    }

    public function test_DosisSuperaElMaximo()
    {
        $service = $this->crearServicio($this->modeloNuevoProducto());

        $this->expectException(\InvalidArgumentException::class);
        $service->crearProducto(array_merge($this->datosValidos(), ['dosis_producto' => 3001]));
    }

    public function test_DosisJustoEnElMaximo()
    {
        $model = $this->modeloNuevoProducto();
        $service = $this->crearServicio($model);

        // 3000 es el límite superior válido
        $this->assertIsInt($service->crearProducto(array_merge($this->datosValidos(), ['dosis_producto' => 3000])));
    }

    public function test_DosisJustoEnElMinimo()
    {
        $model = $this->modeloNuevoProducto();
        $service = $this->crearServicio($model);

        // 0.01 es el mínimo válido
        $this->assertIsInt($service->crearProducto(array_merge($this->datosValidos(), ['dosis_producto' => 0.01])));
    }

    // Dosis con notacion cientifica grande
    public function test_DosisNotacionCientificaGrande()
    {
        $service = $this->crearServicio($this->modeloNuevoProducto());

        $this->expectException(\InvalidArgumentException::class);
        $service->crearProducto(array_merge($this->datosValidos(), ['dosis_producto' => 1e5]));
    }


    // **** VALIDACIONES CON LOS IDS DE LOS OTROS OBJETOS ****
    public function test_IDTipoProductoCero()
    {
        $service = $this->crearServicio($this->modeloNuevoProducto());

        $this->expectException(\InvalidArgumentException::class);
        $service->crearProducto(array_merge($this->datosValidos(), ['id_tipo_producto' => 0]));
    }

    public function test_IDMedidaProductoNegativoLanzaExcepcion()
    {
        $service = $this->crearServicio($this->modeloNuevoProducto());

        $this->expectException(\InvalidArgumentException::class);
        $service->crearProducto(array_merge($this->datosValidos(), ['id_medida_producto' => -3]));
    }

    // **** VALIDACIÓN DE DESCRIPCIÓN ****

    public function test_DescripcionNula()
    {
        $model = $this->modeloNuevoProducto();
        $service = $this->crearServicio($model);

        $this->assertIsInt($service->crearProducto(array_merge($this->datosValidos(), ['descripcion_producto' => null])));
    }

    public function test_DescripcionVacia()
    {
        $model = $this->modeloNuevoProducto();
        $service = $this->crearServicio($model);

        $this->assertIsInt($service->crearProducto(array_merge($this->datosValidos(), ['descripcion_producto' => ''])));
    }

    public function test_DescripcionConCaracteresPermitidos()
    {
        $model = $this->modeloNuevoProducto();
        $service = $this->crearServicio($model);

        $this->assertIsInt($service->crearProducto(array_merge($this->datosValidos(), ['descripcion_producto' => 'Comprimido recubierto (500mg)'])));
    }

    public function test_DescripcionConPorcentaje()
    {
        $model = $this->modeloNuevoProducto();
        $service = $this->crearServicio($model);

        $this->assertIsInt($service->crearProducto(array_merge($this->datosValidos(), ['descripcion_producto' => 'Solucion 10%'])));
    }

    public function test_DescripcionConCaracterInvalido()
    {
        $service = $this->crearServicio($this->modeloNuevoProducto());

        $this->expectException(\InvalidArgumentException::class);
        $service->crearProducto(array_merge($this->datosValidos(), ['descripcion_producto' => 'Comprimido#especial']));
    }

    // **** PRODUCTO YA EXISTE Y ESTA ACTICO ****

    public function test_ProductoExistenteActivo()
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);
        $model->method('productoFarmaceuticoUnico')->willReturn(false);
        $model->method('buscarProductoExistente')->willReturn($this->crearProductoMock(activo: true));

        $service = $this->crearServicio($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->crearProducto($this->datosValidos());
    }

    // Verifica que agregar no se llama si el producto ya existe y está activo
    public function test_AgregarNoSeLlamaSiProductoActivoExiste()
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);
        $model->method('productoFarmaceuticoUnico')->willReturn(false);
        $model->method('buscarProductoExistente')->willReturn($this->crearProductoMock(activo: true));
        $model->expects($this->never())->method('agregar');

        $service = $this->crearServicio($model);

        try {
            $service->crearProducto($this->datosValidos());
        } catch (\InvalidArgumentException $e) {
            // esperado
        }
    }


    // PRODUCTO YA EXISTE Y ESTA INACTIVO

    public function test_ProductoInactivoSeReactiva()
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);
        $model->method('productoFarmaceuticoUnico')->willReturn(false);
        $model->method('buscarProductoExistente')->willReturn($this->crearProductoMock(activo: false, id: 10));

        $model->expects($this->once())
            ->method('reactivar')
            ->with(10);

        $service = $this->crearServicio($model);
        $result = $service->crearProducto($this->datosValidos());

        $this->assertSame(10, $result);
    }

    // Verifica que agregar NO se llama cuando se reactiva
    public function test_AgregarNoSeLlamaCuandoSeReactiva()
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);
        $model->method('productoFarmaceuticoUnico')->willReturn(false);
        $model->method('buscarProductoExistente')->willReturn($this->crearProductoMock(activo: false, id: 10));
        $model->expects($this->never())->method('agregar');

        $service = $this->crearServicio($model);
        $service->crearProducto($this->datosValidos());
    }


    // **** VALIDACION UNICIDAD ****
    public function test_ProductoDuplicado()
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);
        // productoFarmaceuticoUnico dice que ya existe
        $model->method('productoFarmaceuticoUnico')->willReturn(true);
        $model->method('buscarProductoExistente')->willReturn(null);

        $service = $this->crearServicio($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->crearProducto($this->datosValidos());
    }

    public function test_IDMedicamentoAusente()
    {
        $service = $this->crearServicio($this->modeloNuevoProducto());
        $data = $this->datosValidos();
        unset($data['id_medicamento']);

        $this->expectException(\InvalidArgumentException::class);
        $service->crearProducto($data);
    }

    // **** FALLA DE BASE DE DATOS ****

    public function test_FallaBDAlAgregar()
    {
        $model = $this->createMock(ProductoFarmaceuticoModel::class);
        $model->method('productoFarmaceuticoUnico')->willReturn(false);
        $model->method('buscarProductoExistente')->willReturn(null);
        $model->method('agregar')->willReturn(0);

        $service = $this->crearServicio($model);

        $this->expectException(\RuntimeException::class);
        $service->crearProducto($this->datosValidos());
    }
}