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
        if ($this->request->getMethod() === 'post') {
            $nombre = $this->request->getPost('nombre_medicamento');

            try {
                //Le pasa el nombre del medicamento al service para que lo verifique
                $id = $this->medicamentoService->crearMedicamento($nombre);
                return redirect()->to('/medicamentos')//Si hubo éxito redirije a la vista de medicamentos
                                ->with('success', 'Medicamento creado correctamente.');
            } catch (\InvalidArgumentException $e) {
                return redirect()->back()
                                ->withInput()//Con los inputs ingresados
                                ->with('error', $e->getMessage());
            } catch (\Exception $e) {
                //Guarda el error tal cual es para su posteior arreglo y al usuario le muestra un msg amigable.
                log_message('error', '[Medicamentos::create] ' . $e->getMessage());
                return redirect()->back()
                                ->withInput()
                                ->with('error', 'Error inesperado al crear el medicamento.');
            }
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