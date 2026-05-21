<?php

namespace App\Controllers;

use App\Services\MedicamentoService;
use App\Services\MedidaProductoService;
use App\Services\TipoProductoService;
use App\Services\ProductoFarmaceuticoService;

class MedicamentosController extends BaseController
{
    //Se crea la variable a utilizar del servicio de los medicamentos
    protected $medicamentoService;
    protected $unidadesMedidaService;
    protected $tiposProductoService;
    protected $productoFarmaceuticoService;

    /*Creacion del constructor para evitar llamar al servicio en cada funcion*/
    public function __construct()
    {
        //Se instancian los servicios
        $this->medicamentoService = new MedicamentoService();
        $this->unidadesMedidaService = new MedidaProductoService();
        $this->tiposProductoService = new TipoProductoService();
        $this->productoFarmaceuticoService = new ProductoFarmaceuticoService();
    }

    public function obtenerDatosAuxiliares(): array
    {
        return [
            'medicamentos'=> $this->medicamentoService->obtenerMedicamentosDropdown(),
            'unidadesMedida'=> $this->unidadesMedidaService->obtenerMedidaDropdown(),
            'tiposProducto'=> $this->tiposProductoService->obtenerTiposDropdown()
        ];
    }

    /*Metodo que carga los datos a la vista de la creacion de los medicamentos*/
    public function vista_alta_medicamentos(): string
    {
        $data = $this->obtenerDatosAuxiliares();
        return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/creacion_medicamento', $data)
        ]);
    }

    public function obtenerDatosMedicamentoPost() : array 
    {
        return [
            'idMedicamento'=> $this->request->getPost('id_medicamento'),//Asigna a esa variable el id del medicamento del post
            'nombreMedicamento'=> $this->request->getPost('nombre_medicamento'),//Asigna a esa variable el nombre del medicamento del post
        ];
    }

    public function obtenerDatosProductoPost() : array{
         return [
            'idMedicamento'=> $this->request->getPost('id_medicamento'),//Asigna a esa variable el id del medicamento del post,
            'idProducto' => $this->request->getPost('id_producto'), //id del producto modificado
            'id_tipo_producto' => (int) $this->request->getPost('id_tipo_producto'),
            'id_medida_producto' => (int) $this->request->getPost('id_medida_producto'),
            'dosis_producto' => $this->request->getPost('dosis_producto'),
            'descripcion_producto' => $this->request->getPost('descripcion_producto') ?: null,
        ];
    }

    /*Metodo para la creacion de los medicamentos (form POST). Se hace uso de transacciones para poder manejar posibles 
    errores en caso de que se inserte un nuevo medicamento pero no un nuevo producto farmaceutico, dejando incosistencias en la bd.*/
    public function altaMedicamento()
    {
        $db = \Config\Database::connect();//Se crea la conexión con la BD
        $db->transBegin();//Comienza la transaccion

        try{
            $dataMed = $this->obtenerDatosMedicamentoPost();
            $dataProd = $this->obtenerDatosProductoPost();

            //Se llama al servicio encargado de la creacion del nuevo producto

            //Se determina mediante un if (a modo de filtro) si el ID del medicamento es nuevo o es para un nuevo producto farmaceutico.
            if ($dataMed['idMedicamento'] === 'new') {
                //Llamamos al servicio de medicamento para la creacion del mismo
                $idMedicamentoNuevo = $this->medicamentoService->crearMedicamento($dataMed['nombreMedicamento']);
            } else {
                $idMedicamentoNuevo = (int) $dataMed['idMedicamento'];//El medicamento no es nuevo si no que fue seleccionado del dropdown
                $this->medicamentoService->buscarMedicamentoPorID($idMedicamentoNuevo);//Se realiza una validacion llamando al model de medicamentos
            }

            $idNuevoMedicamento = $this->medicamentoService->crearMedicamento($dataMed['nombreMedicamento']);
            $dataProd['idMedicamento'] = $idNuevoMedicamento;
            $this->productoFarmaceuticoService->crearProducto($dataProd);

            $db->transCommit();

            return redirect()->to('/')->with('success', 'Medicamento y/o producto creados correctamente.');
            //Manejo de errores de los posibles rollbacks en caso de fallas.
            } catch (\Exception $e) {
                $db->transRollback();
                throw $e;
            }
    }

    /*Metodo que carga los datos a la vista de la modificacion de los medicamentos*/
    public function vista_modificacion_medicamento(): string
    {
        $data = $this->obtenerDatosAuxiliares();
        return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/modificacion_medicamento', $data)
        ]);
    }


    /*Metodo para la modificacion de los medicamentos (form POST)
    public function modificacionMedicamento()
    {
        $huboCambios = false;//Variable para detectar si hubo cambios y proporcionar el msj correcto
        $db = \Config\Database::connect();//Se hace la conexión a la bd.
        $db->transBegin();//Se inicia la transacción.

        try {//Se obtienen los valores del post
            $data = $this->obtenerDatosModificacion();

            //Primero se valida el id del medicamento
            if (!$idMedicamento) {
                throw new \Exception("Medicamento inválido");
            }

            //Luego de la validación, se hace uso del servicio de medicamento para la modificacion del nombre del mismo (caso que corresponda)
            if (!empty($nombreMedicamento)) {
                $nuevoMed = $this->medicamentoService->modificarMedicamento($idMedicamento, $nombreMedicamento);

                //Comprobacion si hubo cambios o no
                if($nuevoMed){
                    $huboCambios = true;
                }
            }

            //En caso de que se haya actualizado algun dato del producto farmaceutico, entra acá
            if ($idProducto && $idProducto != "-1") {//Si se selecciono un producto para modificar

                $productoData = [
                    'id_producto' => (int) $idProducto,
                    'id_tipo_producto' => (int) $this->request->getPost('id_tipo_producto'),
                    'id_medida_producto' => (int) $this->request->getPost('id_medida_producto'),
                    'id_medicamento' => (int) $idMedicamento,
                    'dosis_producto' => $this->request->getPost('dosis_producto'),
                    'descripcion_producto' => $this->request->getPost('descripcion_producto')
                ];
                $nuevoProd = $this->productoFarmaceuticoService->modificarProductoFarmaceutico($idProducto, $productoData);
                
                //Comprobacion si hubo cambios o no
                if($nuevoProd){
                    $huboCambios = true;
                }
            }


            $db->transCommit();//Cierra la transacción

            //Si no se modifico nada
            if (!$huboCambios) {
                return redirect()->back()->with('info', 'No se realizaron cambios.');
            }

            return redirect()->to('/modificacion_medicamento')->with('success', 'Modificación realizada correctamente');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /*Metodo para obtener los productos farmaceuticos por medicamento seleccionado.
     * Retorna JSON.
     * Es usada en el UPDATE.
     */
    public function productosPorMedicamento(int $idMedicamento)
    {
        $productoFarmaceutico = $this->productoFarmaceuticoService->obtenerProductosPorMedicamento($idMedicamento);
        return $this->response->setJSON($productoFarmaceutico);
    }

    /*Metodo que carga los datos a la vista de la eliminacion de los medicamentos*/
    public function vista_baja_medicamento(): string
    {
        $data = $this->obtenerDatosAuxiliares();
        return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/eliminacion_medicamento', ['medicamentos' => $data['medicamentos']])
        ]);
    }

    /*Metodo para la eliminacion de los medicamentos y productos farmaceuticos*/
    public function bajaMedicamento($idMedicamento)
    {
        $db = \Config\Database::connect();//Conexion a la bd
        $db->transBegin(); //Se inicia la transaccion
        
        try {            
            $this->medicamentoService->eliminarMedicamento((int)$idMedicamento);//Llamada al service para su eliminacion

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