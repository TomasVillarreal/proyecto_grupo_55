<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\PedidoService;
use App\Models\PedidoModel;
use App\Entities\Pedido;
use App\Entities\EstadoPedido;

final class RechazarPedidoTest extends CIUnitTestCase
{
    
    // Creacion de un estado mockeado
    private function crearEstadoMock(int $id): EstadoPedido
    {
        $estado = $this->createMock(EstadoPedido::class);
        $estado->method('obtenerID')->willReturn($id);
        return $estado;
    }

    // Creacion de un pedido mockeado
    private function crearPedidoMock(int $estadoID, int $pedidoID = 1): Pedido
    {
        $pedido = $this->createMock(Pedido::class);
        $pedido->method('obtenerID')->willReturn($pedidoID);
        $pedido->method('obtenerEstado')->willReturn($this->crearEstadoMock($estadoID));
        return $pedido;
    }

    // Mensaje válido por defecto para tests que no prueban el mensaje
    private string $mensajeValido = 'Falta documentación obligatoria del paciente.';

    // **** PEDIDO NO EXISTE ****

    public function test_RechazarPedidoInexistente()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn(null);

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->rechazarPedido(999, $this->mensajeValido);
    }


    // **** ESTADO INVÁLIDO PARA RECHAZAR ****

    public function test_RechazarPedidoAprobado()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 2));

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->rechazarPedido(1, $this->mensajeValido);
    }

    public function test_RechazarPedidoRechazado()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 3));

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->rechazarPedido(1, $this->mensajeValido);
    }

    public function test_RechazarPedidoConEstadoDesconocido()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 99));

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->rechazarPedido(1, $this->mensajeValido);
    }


    // **** VALIDACIÓN DEL MENSAJE - casos malos****

    public function test_MensajeVacio()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 1));

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->rechazarPedido(1, '');
    }

    public function test_MensajeSoloEspacios()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 1));

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->rechazarPedido(1, '          ');
    }

    public function test_MensajeMuyCorto()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 1));

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->rechazarPedido(1, 'Corto'); // menos de 10 caracteres
    }

    public function test_MensajeLimiteLongitud()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 1));

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->rechazarPedido(1, 'Abcdefgh'); // 8 caracteres, mínimo es 10
    }

    public function test_MensajeMuyLargo()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 1));

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->rechazarPedido(1, str_repeat('a', 256)); // supera 255
    }

    public function test_MensajeSoloNumeros()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 1));

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->rechazarPedido(1, '1234567890'); // solo números, sin letra
    }

    public function test_MensajeCaracteresInvalidos()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 1));

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->rechazarPedido(1, 'Motivo inválido con @ y # caracteres');
    }


    // **** VALIDACIÓN DEL MENSAJE — buenos casos ****

    public function test_MensajeLimiteLongitudPositivo()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 1));
        $model->method('rechazar')->willReturn(true);

        $service = new PedidoService($model);

        // exactamente 10 caracteres con al menos una letra
        $this->assertTrue($service->rechazarPedido(1, 'Motivo: ok'));
    }

    public function test_MensajeLimiteLongitudMaxima()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 1));
        $model->method('rechazar')->willReturn(true);

        $service = new PedidoService($model);

        // exactamente 255 caracteres
        $mensaje = str_repeat('a', 255);
        $this->assertTrue($service->rechazarPedido(1, $mensaje));
    }

    public function test_MensajeConCaracteresPermitidos()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 1));
        $model->method('rechazar')->willReturn(true);

        $service = new PedidoService($model);

        // letras, números, espacios, puntos, comas, guiones, paréntesis, punto y coma, dos puntos, barra
        $this->assertTrue($service->rechazarPedido(1, 'Rechazo por falta de stock (lote 3); ver nota 1/2.'));
    }

    public function test_MensajeConTildesYEnie()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 1));
        $model->method('rechazar')->willReturn(true);

        $service = new PedidoService($model);

        $this->assertTrue($service->rechazarPedido(1, 'Medicación no disponible según indicación médica.'));
    }


    // Casos del guion '-'.
    public function test_Guion()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 1));
        $model->method('rechazar')->willReturn(true);

        $service = new PedidoService($model);

        $this->assertTrue($service->rechazarPedido(1, '-'));
    }

    public function test_GuionConEspacios()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 1));
        $model->method('rechazar')->willReturn(true);

        $service = new PedidoService($model);

        $this->assertTrue($service->rechazarPedido(1, '  -  '));
    }

    public function test_DobleGuion()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 1));

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->rechazarPedido(1, '--');
    }


    // **** RECHAZAR EXITOSO ****

    public function test_RechazarPedidoPendiente()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 1, pedidoID: 1));
        $model->method('rechazar')->willReturn(true);

        $service = new PedidoService($model);

        $this->assertTrue($service->rechazarPedido(1, $this->mensajeValido));
    }

    // =============================================
    // INTENTANDO ROMPER: fallo silencioso de BD
    // =============================================

    // caso donde rechazar devuelva falso, y viendo si la funcion devuelve falso
    public function test_FallaBDAlRechazarDevuelveFalseSinExcepcion()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearPedidoMock(estadoID: 1));
        $model->method('rechazar')->willReturn(false);

        $service = new PedidoService($model);

        $result = $service->rechazarPedido(1, $this->mensajeValido);
        $this->assertFalse($result);
    }


    // testeos donde se usan ids invalidos
    public function test_IDCeroLanzaExcepcion()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn(null);

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->rechazarPedido(0, $this->mensajeValido);
    }

    public function test_IDNegativoLanzaExcepcion()
    {
        $model = $this->createMock(PedidoModel::class);
        $model->method('obtenerPorID')->willReturn(null);

        $service = new PedidoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->rechazarPedido(-5, $this->mensajeValido);
    }
}