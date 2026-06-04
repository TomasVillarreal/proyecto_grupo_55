<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\PedidoService;
use App\Models\PedidoModel;
use App\Entities\Pedido;
use App\Entities\EstadoPedido;

final class AprobarPedidoTest extends CIUnitTestCase
{
    // Creacion de estados de mockeo y de pedidos de mockeo
    private function crearEstadoMock(int $id): EstadoPedido
    {
        $estado = $this->createMock(EstadoPedido::class);
        $estado->method('obtenerID')->willReturn($id);
        return $estado;
    }

    // id de estado: 1=Pendiente, 2=Aprobado, 3=Rechazado
    private function crearPedidoMock(int $estadoID, int $pedidoID = 1): Pedido
    {
        $pedido = $this->createMock(Pedido::class);
        $pedido->method('obtenerID')->willReturn($pedidoID);
        $pedido->method('obtenerEstado')->willReturn($this->crearEstadoMock($estadoID));
        return $pedido;
    }

    // **** PEDIDO NO EXISTE ****

    public function test_AprobarPedidoInexistente()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn(null);

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->aprobarPedido(999);
    }


    // **** ESTADO INVÁLIDO PARA APROBAR ****

    public function test_AprobarPedidoAprobado()
    {
        $model = $this->createMock(PedidoModel::class);
        // estado 2 = ya aprobado
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 2));

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->aprobarPedido(1);
    }

    public function test_AprobarPedidoRechazado()
    {
        $model = $this->createMock(PedidoModel::class);
        // estado 3 = ya rechazado
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 3));

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->aprobarPedido(1);
    }

    // Estado desconocido/inesperado 
    public function test_AprobarPedidoEstadoDesconocido()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 99));

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->aprobarPedido(1);
    }

    // **** APROBACIÓN EXITOSA ****

    public function test_AprobarPedidoPendiente()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 1, pedidoID: 1));
        $model->method('aprobar')->willReturn(true);

        $service = new PedidoService($model);

        $this->assertTrue($service->aprobarPedido(1));
    }

    // Verifica que aprobar se llama con el ID que viene de la entidad,
    // no con cualquier valor que llegue como parámetro
    public function test_AprobarSeLlamaConElIDCorrecto()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 1, pedidoID: 5));

        $model->expects($this->once())
            ->method('aprobar')
            ->with(5)
            ->willReturn(true);

        $service = new PedidoService($model);

        $service->aprobarPedido(5);
    }


    // **** OTRAS PRUEBAS ****
    // Verificacion de que sucederia si la funcion aprobar devuelve falso
    public function test_FallaBDAlAprobarDevuelveFalseSinExcepcion()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 1));
        $model->method('aprobar')->willReturn(false); // BD falla

        $service = new PedidoService($model);

        // Actualmente devuelve false en silencio — sin excepción
        $result = $service->aprobarPedido(1);
        $this->assertFalse($result);
    }


    // Testeos con ids invalidos sobre la funcion de aprobar
    public function test_IDCero()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn(null);

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->aprobarPedido(0);
    }

    public function test_IDNegativo()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn(null);

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->aprobarPedido(-10);
    }
}