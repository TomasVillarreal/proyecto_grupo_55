<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\MedicamentoService;
use App\Models\MedicamentoModel;
use App\Entities\Medicamento;

final class EliminarMedicamentoTest extends CIUnitTestCase
{
    // Helper para la creacion de una entidad
    private function crearEntidadMock(bool $activo = true, int $id = 1): Medicamento
    {
        $entity = $this->createMock(Medicamento::class);
        $entity->method('obtenerActivo')->willReturn($activo);
        $entity->method('obtenerID')->willReturn($id);
        return $entity;
    }

    // *** MEDICAMENTO NO EXISTE ****

    public function test_EliminarMedicamentoInexistente()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn(null);

        $service = new MedicamentoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->eliminarMedicamento(999);
    }


    // **** MEDICAMENTO YA INACTIVO ****

    public function test_EliminarMedicamentoYaInactivo()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock(activo: false));

        $service = new MedicamentoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->eliminarMedicamento(1);
    }

    public function test_EliminarMedicamentoInactivoConDistintoID()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock(activo: false, id: 42));

        $service = new MedicamentoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->eliminarMedicamento(42);
    }


    // **** ELIMINACIÓN EXITOSA ****

    public function test_EliminarMedicamentoActivo()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock(activo: true, id: 1));
        $model->method('desactivar')->willReturn(true);

        $service = new MedicamentoService($model);

        $this->assertTrue($service->eliminarMedicamento(1));
    }

    // Verifica que desactivar se llama con el ID correcto de la entidad,
    // no con cualquier cosa que venga de afuera
    public function DesactivarInvocadoConIdCorrecto()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock(activo: true, id: 7));

        $model->expects($this->once())
            ->method('desactivar')
            ->with(7)
            ->willReturn(true);

        $service = new MedicamentoService($model);

        $service->eliminarMedicamento(7);
    }

    // **** OTRAS POSIBLES FALLAS ****

    // prueba q sucede si la funcion desactivar del model devuelta false (por posible error de BD)
    public function test_DesactivarDevuelveFalse()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock(activo: true, id: 1));
        $model->method('desactivar')->willReturn(false); // BD falla

        $service = new MedicamentoService($model);

        // Actualmente devuelve false en silencio — sin excepción
        $result = $service->eliminarMedicamento(1);
        $this->assertFalse($result);
    }

    // prueba de que sucede si el id pasado como argumento es 0 (un id invalido)
    public function test_IDCero()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn(null); // modelo devuelve null para ID 0

        $service = new MedicamentoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->eliminarMedicamento(0);
    }

    // prueba de que sucede con un id negativo (invalido)
    public function test_IDNegativo()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn(null);

        $service = new MedicamentoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->eliminarMedicamento(-5);
    }
}