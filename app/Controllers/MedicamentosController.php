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

    /*Metodo que carga los datos a la vista de la creacion de los medicamentos*/
    public function vista_alta_medicamentos(): string
    {
        $medicamentos = $this->medicamentoService->obtenerMedicamentosDropdown();
        $unidadMedida = $this->unidadesMedidaService->obtenerMedidaDropdown();
        $tiposProducto = $this->tiposProductoService->obtenerTiposDropdown();

        return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/creacion_medicamento', ['medicamentos'=>$medicamentos, 'unidadesMedida'=>$unidadMedida, 'tiposProducto'=>$tiposProducto])
        ]);
    }

    /*Metodo para la creacion de los medicamentos (form POST)
    Se hace uso de transacciones para poder manejar posibles 
    errores en caso de que se inserte un nuevo medicamento
    pero no un nuevo producto farmaceutico, dejando incosistencias
    en la bd.*/
    public function altaMedicamento()
    {
            $db = \Config\Database::connect();//Se hace la conexión a la bd.
            $db->transBegin();//Para iniciar la transacción

        try {
            //Tomamos daots del medicamento (del post)
            $idMedicamentoPost= $this->request->getPost('id_medicamento');//Asigna a esa variable el id del medicamento del post
            $nombreMedicamentoPost = $this->request->getPost('nombre_medicamento');//Asigna a esa variable el nombre del medicamento del post

            //Tomamos datos del producto farmaceutico (del post)
            $productoData = [
                'id_medicamento' => null,
                'id_tipo_producto' => (int) $this->request->getPost('id_tipo_producto'),
                'id_medida_producto' => (int) $this->request->getPost('id_medida_producto'),
                'dosis_producto' => $this->request->getPost('dosis_producto'),
                'descripcion_producto' => $this->request->getPost('descripcion_producto') ?: null,
            ];

            //Se determina mediante un if (a modo de filtro) si el ID del medicamento es nuevo o es para un nuevo producto farmaceutico.
            if($idMedicamentoPost === 'new'){//Verifica si lo que recibe del post es un nuevo id de medicamento
                if(empty($nombreMedicamentoPost)){
                    throw new \Exception("Debe ingresar el nombre del medicamento");
                }
                
                $idMedicamento = $this->medicamentoService->crearMedicamento($nombreMedicamentoPost);//Se crea un nuevo medicamento haciendo uso del service de medicamento.

            } else {//El medicamento no es nuevo si no que fue seleccionado del dropdown
                $idMedicamento = (int) $idMedicamentoPost;

                //Se verifica que el medicamento esté activo
                $medicamentoModel = model('App\Models\MedicamentoModel');
                $medicamentoActivo = $medicamentoModel->find($idMedicamento);
                if(!$medicamentoActivo || !$medicamentoActivo->activo_medicamento){//Verifica si existe en la bd y/o si esta activo
                    throw new \InvalidArgumentException('El medicamento seleccionado no es válido o está inactivo.');
                }
            }

            //Se crea el producto farmaceutico
            $productoData['id_medicamento'] = $idMedicamento;
            $idProducto = $this->productoFarmaceuticoService->crearProducto($productoData);

            $db->transCommit(); //Si todo salió bien, se confirma la transacción
            
            return redirect()->to('/')->with('success', 'Medicamento y/o producto creados correctamente.');//Si todo es un éxito.

        //Manejo de errores de los posibles rollbacks en caso de fallas.
        } catch (\InvalidArgumentException $e) {
            $db->transRollback(); //Revierte todos los cambios si hay error de validación
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            $db->transRollback(); //Revierte todos los cambios si hay error inesperado
            log_message('error', '[altaMedicamento] ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Ocurrió un error. Producto farmaceutico ya ingresado!');
        }
    }

    /*Metodo que carga los datos a la vista de la modificacion de los medicamentos*/
    public function vista_modificacion_medicamento(): string
    {
        $medicamentos = $this->medicamentoService->obtenerMedicamentosDropdown();
        $unidadMedida = $this->unidadesMedidaService->obtenerMedidaDropdown();
        $tiposProducto = $this->tiposProductoService->obtenerTiposDropdown();

            return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/modificacion_medicamento', ['medicamentos'=>$medicamentos, 'unidadesMedida'=>$unidadMedida, 'tiposProducto'=>$tiposProducto])
        ]);
    }

    /*Metodo para la modificacion de los medicamentos (form POST)*/
    public function modificacionMedicamento()
    {
        $huboCambios = false;//Variable para detectar si hubo cambios y proporcionar el msj correcto
        $db = \Config\Database::connect();//Se hace la conexión a la bd.
        $db->transBegin();//Se inicia la transacción.

        try {//Se obtienen los valores del post
            $idMedicamento = (int) $this->request->getPost('id_medicamento');//id del medicamento modificado (o su producto)
            $nombreMedicamento = $this->request->getPost('nombre_medicamento');//nombre del medicamento modificado (o su producto)
            $idProducto = $this->request->getPost('id_producto');//id del producto modificado

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
        $medicamentos = $this->medicamentoService->obtenerMedicamentosDropdown();

            return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/eliminacion_medicamento', ['medicamentos'=>$medicamentos])
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