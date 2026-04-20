<?php

namespace App\Controllers;

use App\Models\MedicamentoModel;
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
    public function create(): string
    {
        $medicamentos = $this->medicamentoService->obtenerMedicamentosDropdown();
        $unidadMedida = $this->unidadesMedidaService->obtenerMedidaDropdown();
        $tiposProducto = $this->tiposProductoService->obtenerTiposDropdown();

        return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/create', ['medicamentos'=>$medicamentos, 'unidadesMedida'=>$unidadMedida, 'tiposProducto'=>$tiposProducto])
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
            dd($this->request->getPost());
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

                $idMedicamentoNuevo = $this->medicamentoService->crearMedicamento($nombreMedicamentoPost);//Se crea un nuevo medicamento haciendo uso del service de medicamento.
                $productoData['id_medicamento'] = $idMedicamentoNuevo;


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
            
            return redirect()->to('/')->with('success', 'Medicamento y producto creados correctamente.');//Si todo es un éxito.

        //Manejo de errores de los posibles rollbacks en caso de fallas.
        } catch (\InvalidArgumentException $e) {
            $db->transRollback(); //Revierte todos los cambios si hay error de validación
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            $db->transRollback(); //Revierte todos los cambios si hay error inesperado
            log_message('error', '[altaMedicamento] ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Ocurrió un error inesperado al procesar la solicitud.');
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

    /*public function update(): string
    {
        $medicamentoModel = new MedicamentoModel();
        $tipoProductoModel = new TipoProductoModel();
        $medidaModel = new MedidaProductoModel();

        $tiposProductos = $tipoProductoModel->obtenerParaDropdown();
        $unidadesMedida = $medidaModel->obtenerParaDropdown();
        $medicamentos = $medicamentoModel->obtenerMedicamentosActivos();

            return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/update', ['medicamentos'=>$medicamentos, 'unidadesMedida'=>$unidadesMedida, 'tiposProducto'=>$tiposProductos])
        ]);
    }*/

    /*public function delete(): string
    {
        $medicamentoModel = new MedicamentoModel();

        $medicamentos = $medicamentoModel->findAll();

            return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/delete', ['medicamentos'=>$medicamentos])
        ]);
    }*/
}

    /*
    //Verificacion si es POST y se asigna al campo el nombre de medicamento ingresado.

    */


/*
public function productosPorMedicamento($idMedicamento)
    {
        $productoModel = new \App\Models\ProductoFarmaceuticoModel();

        $productos = $productoModel->obtenerProductosPorMedicamento((int)$idMedicamento);

        return $this->response->setJSON($productos);
    }
}
*/


//CODIGO CON SERVICES

/*
    /*Funcion para modificar los datos de un medicamento existente junto con
    el procesamiento de los datos POST
    public function update(int $idMedicamento)
    {
        $medicamentoModel = model('App\Models\MedicamentoModel');
        $medicamento = $medicamentoModel->find($idMedicamento);

        if (!$medicamento) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            //En caso de que no se encuentre el medicamento se muestra una vista amigable al usuario
        }

        //POST para procesar la actualización/modificacion
        if ($this->request->getMethod() === 'post') {
            $nombre = $this->request->getPost('nombre_medicamento');

            try {
                //Se hace uso del metodo del servicio de medicamento
                $this->medicamentoService->modificarMedicamento($idMedicamento, $nombre);
                return redirect()->to('/medicamentos')
                                ->with('success', 'Medicamento actualizado correctamente.');
            } catch (\InvalidArgumentException $e) {
                return redirect()->back()
                                ->withInput()
                                ->with('error', $e->getMessage());
            } catch (\Exception $e) {
                log_message('error', '[Medicamentos::update] ' . $e->getMessage());
                return redirect()->back()
                                ->withInput()
                                ->with('error', 'Error inesperado al actualizar el medicamento.');
            }
        }

        //Se muestra el formulario con los datos actualizados
        return view('layout/main_layout', [
            'title'   => 'Editar Medicamento - Clinicks',
            'content' => view('medicamentos/update', ['medicamento' => $medicamento])
        ]);
    }**/


/*Metodo para el procesamiento de la eliminación l´ogica de un medicamento
    public function delete(int $idMedicamento)
    {
        $medicamentoModel = model('App\Models\MedicamentoModel');
        $medicamento = $medicamentoModel->find($idMedicamento);

        if (!$medicamento) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        //Si el form es POST, se ejecuta la eliminación (baja)
        if ($this->request->getMethod() === 'post') {
            try {
                //Se hace uso del metodo del service de medicamento
                $this->medicamentoService->eliminarMedicamento($idMedicamento);
                return redirect()->to('/medicamentos')
                                ->with('success', 'Medicamento eliminado correctamente.');
            } catch (\Exception $e) {
                log_message('error', '[Medicamentos::delete] ' . $e->getMessage());
                return redirect()->to('/medicamentos')
                                ->with('error', 'No se pudo eliminar el medicamento.');
            }
        }

        //Se muestra la vista
        return view('layout/main_layout', [
            'title'   => 'Eliminar Medicamento - Clinicks',
            'content' => view('medicamentos/delete', ['medicamento' => $medicamento])
        ]);
    } */