<?php

namespace App\Controllers;

use App\Services\ProductoFarmaceuticoService;
use App\Services\MedicamentoService;
use App\Services\MedidaProductoService;
use App\Services\TipoProductoService;

class ProductoFarmaceuticoController extends BaseController
{
    //Se crean las variables a utilizar de los respectivos servicios
    protected ProductoFarmaceuticoService $productoService;
    protected MedicamentoService $medicamentoService;
    protected MedidaProductoService $medidaService;
    protected TipoProductoService $tipoService;

    /*Creacion del constructor para evitar llamar al servicio en cada funcion*/
    public function __construct()
    {
        //Se instancian nuevos objetos de los servicios
        $this->productoService = new ProductoFarmaceuticoService();
        $this->medicamentoService = new MedicamentoService();
        $this->medidaService = new MedidaProductoService();
        $this->tipoService = new TipoProductoService();
    }

    public function obtenerDatosAuxiliares(): array
    {
        return [
            'medicamentos'=> $this->medicamentoService->obtenerMedicamentosDropdown(),
            'unidadesMedida'=> $this->medidaService->obtenerMedidaDropdown(),
            'tiposProducto'=> $this->tipoService->obtenerTiposDropdown()
        ];
    }

    /*Metodo que carga los datos a la vista de la creacion de los medicamentos*/
    public function mostrarAltaProductos(): string
    {
        $data = $this->obtenerDatosAuxiliares();
        return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/creacion_medicamento', $data)
        ]);
    }

    // Metodo que obtiene los datos del post, especificamente del MEDICAMENTO
    public function obtenerDatosMedicamentoPost() : array 
    {
        return [
            'id_medicamento'=> $this->request->getPost('id_medicamento'),//Asigna a esa variable el id del medicamento del post
            'nombre_medicamento'=> $this->request->getPost('nombre_medicamento'),//Asigna a esa variable el nombre del medicamento del post
        ];
    }

    // Metodo que obtiene los datos del post, especificamente del PRODUCTO FARMACEUTICO
    public function obtenerDatosProductoPost() : array{
         return [
            'id_medicamento'=> $this->request->getPost('id_medicamento'),
            'id_producto' => $this->request->getPost('id_producto'),
            'id_tipo_producto' => (int) $this->request->getPost('id_tipo_producto'),
            'id_medida_producto' => (int) $this->request->getPost('id_medida_producto'),
            'dosis_producto' => $this->request->getPost('dosis_producto'),
            'descripcion_producto' => $this->request->getPost('descripcion_producto') ?: null,
        ];
    }

    /*Metodo para la creacion de los medicamentos (form POST). Se hace uso de transacciones para poder manejar posibles 
    errores en caso de que se inserte un nuevo medicamento pero no un nuevo producto farmaceutico, dejando incosistencias en la bd.*/
    public function crearProducto()
    {
        $db = \Config\Database::connect();//Se crea la conexión con la BD
        $db->transBegin();//Comienza la transaccion

        try{
            // agarro la totalidad de datos del form
            $dataMed = $this->obtenerDatosMedicamentoPost();
            $dataProd = $this->obtenerDatosProductoPost();

            //Se determina mediante un if (a modo de filtro) si el ID del medicamento es nuevo o es para un nuevo producto farmaceutico.
            if ($dataMed['id_medicamento'] === 'new') {
                // Si se quiere crear un nuevo med, llamo a la funcion del service correspondiente
                $idMedicamentoNuevo = $this->medicamentoService->crearMedicamento($dataMed['nombre_medicamento']);
                // Y almaceno el id nuevo
                $dataProd['id_medicamento'] = $idMedicamentoNuevo;
            } else {
                // Si no se quiere crear un nuevo med, simplemente guardo el id del med seleccionado
                $idMedicamentoNuevo = (int) $dataMed['id_medicamento'];
            }
            // ahora creo el producto
            $this->productoService->crearProducto($dataProd);
            $db->transCommit();
            return redirect()->to('/')->with('success', 'Medicamento y/o producto creados correctamente.');
            //Manejo de errores de los posibles rollbacks en caso de fallas.
            } catch (\InvalidArgumentException $e) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', $e->getMessage());
            } catch (\Exception $e) {
                $db->transRollback();
                log_message('error', '[altaMedicamento] ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'Ocurrió un error. Producto farmaceutico ya ingresado!');
            }
    }

    /*Metodo que carga los datos a la vista de la modificacion de los medicamentos*/
    public function mostrarModificacionProductos(): string
    {
        $data = $this->obtenerDatosAuxiliares();
        return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/modificacion_medicamento', $data)
        ]);
    }


    /*Metodo para la modificacion de los medicamentos (form POST)*/
    public function modificarProducto()
    {
        $huboCambios = false;//Variable para detectar si hubo cambios y proporcionar el msj correcto
        $db = \Config\Database::connect();//Se hace la conexión a la bd.
        $db->transBegin();//Se inicia la transacción.

        try {//Se obtienen los valores del post
            $dataMed = $this->obtenerDatosMedicamentoPost();
            $dataProd = $this->obtenerDatosProductoPost();

            //Primero se valida el id del medicamento
            if ($dataMed['id_medicamento'] === null) {
                throw new \Exception("Medicamento inválido");
            }

            //Luego de la validación, se hace uso del servicio de medicamento para la modificacion del nombre del mismo (caso que corresponda)
            if (!empty($dataMed['nombre_medicamento'])) {
                $nuevoMed = $this->medicamentoService->modificarMedicamento($dataMed['id_medicamento'], $dataMed['nombre_medicamento']);

                //Comprobacion si hubo cambios o no
                if($nuevoMed){
                    $huboCambios = true;
                }
            }

            //En caso de que se haya actualizado algun dato del producto farmaceutico, entra acá
            if ($dataProd['id_producto'] && $dataProd['id_producto'] != "-1") {//Si se selecciono un producto para modificar
                $nuevoProd = $this->productoService->modificarProductoFarmaceutico($dataProd['id_producto'] , $dataProd);            
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

            return redirect()->back()->with('success', 'Modificación realizada correctamente');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /*Metodo para obtener los productos farmaceuticos por medicamento seleccionado.
     * Retorna JSON.
     * Es usada en el UPDATE.
     */
    public function obtenerProductosPorMedicamento(int $idMedicamento)
    {
        $productoFarmaceutico = $this->productoService->obtenerProductosPorMedicamento($idMedicamento);
        return $this->response->setJSON($productoFarmaceutico);
    }

    /*Metodo que carga los datos a la vista de la eliminacion de los medicamentos*/
    public function mostrarBajaProductos(): string
    {
        $data = $this->obtenerDatosAuxiliares();
        return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/eliminacion_medicamento', ['medicamentos' => $data['medicamentos']])
        ]);
    }


    //Metodo que realiza la eliminacion de un producto farmaceutico
    public function eliminarProducto(int $idProducto)
    {
        try {

            $this->productoService->eliminarProducto((int)$idProducto);

            return redirect()->back()->with('success', 'Producto eliminado correctamente.');

        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());

        } catch (\Exception $e) {
            log_message('error', '[bajaProducto] ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar el producto.');
        }
    }
}