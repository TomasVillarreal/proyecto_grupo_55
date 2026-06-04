<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\MedicamentoService;
use App\Models\MedicamentoModel;
use App\Entities\Medicamento;

final class ModificarMedicamentoTest extends CIUnitTestCase
{
    // Crea una entidad de medicamento para el mocck
    private function crearEntidadMock(string $nombre, bool $activo = true, int $id = 1): Medicamento
    {
        $entity = $this->createMock(Medicamento::class);
        $entity->method('obtenerNombre')->willReturn($nombre);
        $entity->method('obtenerActivo')->willReturn($activo);
        $entity->method('obtenerID')->willReturn($id);
        return $entity;
    }

    // **** MEDICAMENTO NO EXISTE ****

    public function test_ModificarMedicamentoInexistente()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn(null);

        $service = new MedicamentoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->modificarMedicamento(999, 'Ibuprofeno');
    }

    // ***** VALIDACIONES NOMBRE ****

    public function test_NombreVacio()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock('Ibuprofeno'));

        $service = new MedicamentoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->modificarMedicamento(1, '');
    }

    public function test_SoloEspacios()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock('Ibuprofeno'));

        $service = new MedicamentoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->modificarMedicamento(1, '     ');
    }

    public function test_NombreMuyCorto()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock('Ibuprofeno'));

        $service = new MedicamentoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->modificarMedicamento(1, 'ab');
    }

    public function test_SoloNumeros()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock('Ibuprofeno'));

        $service = new MedicamentoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->modificarMedicamento(1, '12345');
    }

    public function test_CaracteresInvalidos()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock('Ibuprofeno'));

        $service = new MedicamentoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->modificarMedicamento(1, 'Ibuprofeno#');
    }

    public function test_NombreConGuionMedio()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock('Ibuprofeno'));

        $service = new MedicamentoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->modificarMedicamento(1, 'Amoxicilina-Acido');
    }

    public function test_DobleEspacio()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock('Ibuprofeno'));

        $service = new MedicamentoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->modificarMedicamento(1, 'Acido  Folico');
    }

    // **** SIN CAMBIOS ****

    public function test_MismoNombreSinCambios()
    {
        $model = $this->createMock(MedicamentoModel::class);
        // El medicamento ya se llama "Ibuprofeno"
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock('Ibuprofeno'));
        $model->method('medicamentoUnico')->willReturn(false);

        $service = new MedicamentoService($model);

        // Debe devolver false porque no hubo cambios
        $this->assertFalse($service->modificarMedicamento(1, 'Ibuprofeno'));
    }

    public function test_MismoNombreConDistintaCaseSinCambios()
    {
        $model = $this->createMock(MedicamentoModel::class);
        // El nombre normalizado de "IBUPROFENO" será "Ibuprofeno", igual al existente
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock('Ibuprofeno'));
        $model->method('medicamentoUnico')->willReturn(false);

        $service = new MedicamentoService($model);

        $this->assertFalse($service->modificarMedicamento(1, 'IBUPROFENO'));
    }

    // **** NOMBRE DUPLICADO ****
    public function test_NombreYaExisteEnOtroMedicamento()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock('Ibuprofeno'));
        // Indica que "Amoxicilina" ya existe para otro ID
        $model->method('medicamentoUnico')->willReturn(true);

        $service = new MedicamentoService($model);

        $this->expectException(\InvalidArgumentException::class);
        $service->modificarMedicamento(1, 'Amoxicilina');
    }

    // **** NORMALIZACIÓN ****

    public function test_NormalizaNombreAlModificar()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock('Ibuprofeno'));
        $model->method('medicamentoUnico')->willReturn(false);

        // Verifica que modificar se llame con el nombre normalizado
        $model->expects($this->once())
            ->method('modificar')
            ->with(1, 'Amoxicilina')
            ->willReturn(true);

        $service = new MedicamentoService($model);

        $result = $service->modificarMedicamento(1, 'AMOXICILINA');
        $this->assertTrue($result);
    }

    public function test_NormalizaConEspaciosAlrededor()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock('Ibuprofeno'));
        $model->method('medicamentoUnico')->willReturn(false);

        $model->expects($this->once())
            ->method('modificar')
            ->with(1, 'Losartan')
            ->willReturn(true);

        $service = new MedicamentoService($model);

        $result = $service->modificarMedicamento(1, '   LOSARTAN   ');
        $this->assertTrue($result);
    }

    // =============================================
    // MODIFICACIÓN EXITOSA
    // =============================================

    public function test_ModificacionExitosa()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock('Ibuprofeno'));
        $model->method('medicamentoUnico')->willReturn(false);
        $model->method('modificar')->willReturn(true);

        $service = new MedicamentoService($model);

        $this->assertTrue($service->modificarMedicamento(1, 'Amoxicilina'));
    }

    public function test_ModificacionConTildeYEnie()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock('Ibuprofeno'));
        $model->method('medicamentoUnico')->willReturn(false);

        $model->expects($this->once())
            ->method('modificar')
            ->with(1, 'Ácido Fólico Niño')
            ->willReturn(true);

        $service = new MedicamentoService($model);

        $this->assertTrue($service->modificarMedicamento(1, 'ÁCIDO FÓLICO NIÑO'));
    }

    public function test_ModificacionConNumeroAlFinal()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorID')->willReturn($this->crearEntidadMock('Ibuprofeno'));
        $model->method('medicamentoUnico')->willReturn(false);
        $model->method('modificar')->willReturn(true);

        $service = new MedicamentoService($model);

        $this->assertTrue($service->modificarMedicamento(1, 'Ibuprofeno 400'));
    }
}