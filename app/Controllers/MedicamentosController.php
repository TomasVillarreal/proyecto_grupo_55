<?php

namespace App\Controllers;

use App\Services\MedicamentoService;
use App\Models\ProductoFarmaceuticoModel;

class Medicamentos extends BaseController
{
    //Se crea la variable a utilizar del servicio de los medicamentos
    protected $medicamentoService;

    /*Creacion del constructor para evitar llamar al servicio en cada funcion*/
    public function __construct()
    {
        //Se instancia un servicio de medicamento
        $this->medicamentoService = new MedicamentoService();
    }

    /*Se obtienen los medicamentos activos utilizando el model de los medicamentos*/
    public function index(): string
    {
        $medicamentoModel = model('App\Models\MedicamentoModel');
        $medicamentos = $medicamentoModel->ObtenerMedicamentosActivos();

        //Retorna a la vista con los medicamentos obtenidos
        return view('layout/main_layout', [
            'title'   => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/index', ['medicamentos' => $medicamentos])
        ]);
    }

    /*Funcion/formulario para crear medicamento o procesar los datos (POST)
     */
    public function create()
    {
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
        }

        //Se muestra el form
        return view('layout/main_layout', [
            'title'   => 'Nuevo Medicamento - Clinicks',
            'content' => view('medicamentos/create')
        ]);
    }

    /*Funcion para modificar los datos de un medicamento existente junto con
    el procesamiento de los datos POST*/
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
    }

    /*Metodo para el procesamiento de la eliminación l´ogica de un medicamento*/
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
    }

    /**
     * Endpoint AJAX para obtener los productos farmacéuticos asociados a un medicamento.
     * Retorna JSON.
     */
    public function productosPorMedicamento(int $idMedicamento)
    {
        $productoModel = model(ProductoFarmaceuticoModel::class);
        $productos = $productoModel->obtenerProductosPorMedicamento($idMedicamento);

        return $this->response->setJSON($productos);
    }
}