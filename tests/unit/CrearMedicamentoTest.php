<?php

namespace Tests\Unit;
use CodeIgniter\Test\CIUnitTestCase;
use App\Services\MedicamentoService;
use App\Models\MedicamentoModel;
use App\Entities\Medicamento;

final class CrearMedicamentoTest extends CIUnitTestCase{

// Funcion que crea el service con el mock del model
    private function crearServicioConMock()
    {
        // Crea un mock del modelo
        $mock = $this->createMock(\App\Models\MedicamentoModel::class);

        // Hace que la funcion del modelo de "obtenerPorNombre" devuelva null
        $mock->method('obtenerPorNombre')->willReturn(null);
        // Y que el agregar devuelva un id cualquiera
        $mock->method('agregar')->willReturn(1);
        // Eso se hace con el fin de que el objeto que se esta por crear no exista previamente en el sistema
        return new \App\Services\MedicamentoService($mock);
    }

    // =============================================
    // VALIDACIÓN DE NOMBRE
    // =============================================
 
    public function test_NombreVacio()
    {
        $service = $this->crearServicioConMock();
 
        $this->expectException(\InvalidArgumentException::class);
        $service->crearMedicamento('');
    }
 
    public function test_SoloEspacios()
    {
        $service = $this->crearServicioConMock();
 
        $this->expectException(\InvalidArgumentException::class);
        $service->crearMedicamento('     ');
    }
 
    public function test_NombreMuyCorto()
    {
        $service = $this->crearServicioConMock();
 
        $this->expectException(\InvalidArgumentException::class);
        $service->crearMedicamento('ab');
    }
 
    public function test_NombreConUnSoloCaracterDespuesDelTrim()
    {
        $service = $this->crearServicioConMock();
 
        $this->expectException(\InvalidArgumentException::class);
        $service->crearMedicamento('  A  '); // después del trim queda "A", muy corto
    }
 
    public function test_NombreDeTresCaracteresEsValido()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorNombre')->willReturn(null);
        $model->method('agregar')->willReturn(5);
 
        $service = new MedicamentoService($model);
 
        $this->assertSame(5, $service->crearMedicamento('Abc'));
    }
 
    public function test_SoloNumeros()
    {
        $service = $this->crearServicioConMock();
 
        $this->expectException(\InvalidArgumentException::class);
        $service->crearMedicamento('12345');
    }
 
    public function test_CaracteresInvalidos()
    {
        $service = $this->crearServicioConMock();
 
        $this->expectException(\InvalidArgumentException::class);
        $service->crearMedicamento('Ibuprofeno#');
    }
 
    public function test_Simbolo()
    {
        $service = $this->crearServicioConMock();
 
        $this->expectException(\InvalidArgumentException::class);
        $service->crearMedicamento('Ibu^profeno');
    }
 
    public function test_NombreConGuionMedio()
    {
        $service = $this->crearServicioConMock();
 
        $this->expectException(\InvalidArgumentException::class);
        $service->crearMedicamento('Amoxicilina-Acido'); // guión no está permitido por el regex
    }
 
    public function test_NombreConPunto()
    {
        $service = $this->crearServicioConMock();
 
        $this->expectException(\InvalidArgumentException::class);
        $service->crearMedicamento('Ibuprofeno.');
    }
 
    public function test_NombreConParentesis()
    {
        $service = $this->crearServicioConMock();
 
        $this->expectException(\InvalidArgumentException::class);
        $service->crearMedicamento('Ibuprofeno (500mg)');
    }
 
    public function test_DobleEspacio()
    {
        $service = $this->crearServicioConMock();
 
        $this->expectException(\InvalidArgumentException::class);
        $service->crearMedicamento('Acido  Folico');
    }
 
    public function test_NombreConDosPalabrasValido()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorNombre')->willReturn(null);
        $model->method('agregar')->willReturn(10);
 
        $service = new MedicamentoService($model);
 
        $this->assertSame(10, $service->crearMedicamento('Acido Folico'));
    }
 
    public function test_NombreConTilde()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorNombre')->willReturn(null);
        $model->method('agregar')->willReturn(3);
 
        $service = new MedicamentoService($model);
 
        $this->assertSame(3, $service->crearMedicamento('Ácido Fólico'));
    }
 
    public function test_NombreConEnie()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorNombre')->willReturn(null);
        $model->method('agregar')->willReturn(4);
 
        $service = new MedicamentoService($model);
 
        $this->assertSame(4, $service->crearMedicamento('Ibuprofeno Niño'));
    }
 
    public function test_NombreConNumeroAlFinal()
    {
        $model = $this->createMock(MedicamentoModel::class);
        $model->method('obtenerPorNombre')->willReturn(null);
        $model->method('agregar')->willReturn(42);
 
        $service = new MedicamentoService($model);
 
        $this->assertSame(42, $service->crearMedicamento('Ibuprofeno 400'));
    }
 
 
    // =============================================
    // NORMALIZACIÓN DE NOMBRE
    // =============================================
 
    public function test_NormalizaNombre()
    {
        $mock = $this->createMock(MedicamentoModel::class);
 
        /* hace que el metodo agregar se INVOQUE una vez durante la prueba
            con el argumento de "Losartan" y se espera que esto devuelva un 1
            osea que el producto no existe, con el fin de comparar los resultados
        */
        $mock->expects($this->once())
            ->method('agregar')
            ->with('Losartan')
            ->willReturn(1);
 
        $service = new MedicamentoService($mock);
 
        // Crea el medicamento y compara el resultado este con el ejecutado con el expects.
        $id = $service->crearMedicamento('LOSARTAN');
        $this->assertSame(1, $id);
    }
 
    public function test_NormalizaMinusculas()
    {
        $model = $this->createMock(MedicamentoModel::class);
 
        $model->expects($this->once())
            ->method('agregar')
            ->with('Amoxicilina')
            ->willReturn(2);
 
        $model->method('obtenerPorNombre')->willReturn(null);
 
        $service = new MedicamentoService($model);
 
        $id = $service->crearMedicamento('amoxicilina');
        $this->assertSame(2, $id);
    }
 
    public function test_NormalizaMixedCase()
    {
        $model = $this->createMock(MedicamentoModel::class);
 
        $model->expects($this->once())
            ->method('agregar')
            ->with('Ibuprofeno')
            ->willReturn(6);
 
        $model->method('obtenerPorNombre')->willReturn(null);
 
        $service = new MedicamentoService($model);
 
        $id = $service->crearMedicamento('iBuPrOfEnO');
        $this->assertSame(6, $id);
    }
 
    public function test_NombreConEspacioAlFinal()
    {
        // trim lo limpia, luego debe normalizarse y crearse bien
        $model = $this->createMock(MedicamentoModel::class);
 
        $model->expects($this->once())
            ->method('agregar')
            ->with('Ibuprofeno')
            ->willReturn(8);
 
        $model->method('obtenerPorNombre')->willReturn(null);
 
        $service = new MedicamentoService($model);
 
        $this->assertSame(8, $service->crearMedicamento('Ibuprofeno   '));
    }
 
 
    // =============================================
    // EXISTENCIA DEL MEDICAMENTO
    // =============================================
 
    // El medicamento existe y esta activo.
    public function test_ExisteActivo()
    {
        $model = $this->createMock(MedicamentoModel::class);
 
        $entity = $this->createMock(Medicamento::class);
        $entity->method('obtenerActivo')->willReturn(true);
 
        $model->method('obtenerPorNombre')->willReturn($entity);
 
        $service = new MedicamentoService($model);
 
        $this->expectException(\InvalidArgumentException::class);
 
        $service->crearMedicamento('Ibuprofeno');
    }
 
    // El medicamento existe y no esta activo
    public function test_ExisteInactivo()
    {
        $model = $this->createMock(MedicamentoModel::class);
 
        $entity = $this->createMock(Medicamento::class);
        $entity->method('obtenerActivo')->willReturn(false);
        $entity->method('obtenerID')->willReturn(7);
 
        $model->method('obtenerPorNombre')->willReturn($entity);
 
        // ejecuta la funcion de reactivar una vez durante la ejecucion y hace que devuelva 7
        $model->expects($this->once())
            ->method('reactivar')
            ->with(7);
 
        $service = new MedicamentoService($model);
 
        // crea un medicamento "existe" e "inactivo" y compara el resultado obtenido con la ejecucion del expects.
        $result = $service->crearMedicamento('Ibuprofeno');
        $this->assertSame(7, $result);
    }
 
 
    // =============================================
    // CREACIÓN EXITOSA
    // =============================================
 
    public function test_CrearNuevo()
    {
        $model = $this->createMock(MedicamentoModel::class);
 
        $model->method('obtenerPorNombre')->willReturn(null);
        $model->method('agregar')->willReturn(42);
 
        $service = new MedicamentoService($model);
 
        $this->assertSame(42, $service->crearMedicamento('Amoxicilina'));
    }
 
 
    // =============================================
    // FALLA DE BASE DE DATOS
    // =============================================
 
    public function test_FallaBD()
    {
        $model = $this->createMock(MedicamentoModel::class);
 
        $model->method('obtenerPorNombre')->willReturn(null);
        $model->method('agregar')->willReturn(0);
 
        $service = new MedicamentoService($model);
 
        $this->expectException(\RuntimeException::class);
 
        $service->crearMedicamento('Amoxicilina');
    }
}