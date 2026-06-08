<?php

namespace App\Controllers;


use App\Services\ProductoFarmaceuticoService;

class MedicamentosController extends BaseController
{
    //Se crea la variable a utilizar del servicio de los medicamentos
    protected ProductoFarmaceuticoService $productoService;

    /*Creacion del constructor para evitar llamar al servicio en cada funcion*/
    public function __construct()
    {
        //Se instancian los servicios
        $this->productoService = new ProductoFarmaceuticoService();
    }

    /*Metodo para la eliminacion de los medicamentos y productos farmaceuticos*/
    public function manejarEliminacionMedicamento(int $idMedicamento)
    {
        $db = \Config\Database::connect();//Conexion a la bd
        $db->transBegin(); //Se inicia la transaccion
        
        try {            
            $this->productoService->eliminarConMedicamento((int)$idMedicamento);//Llamada al service para su eliminacion

            $db->transCommit();//Se finaliza la eliminacion del medicamento

            return redirect()->back()->with('success', 'Medicamento eliminado correctamente.');

        } catch (\InvalidArgumentException $e) {
            $db->transRollback();
            return redirect()->back()->with('error', $e->getMessage());

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[bajaMedicamento] ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar el medicamento.');
        }
    }
}