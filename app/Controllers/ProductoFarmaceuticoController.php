<?php

namespace App\Controllers;

use App\Services\ProductoFarmaceuticoService;

class ProductoFarmaceuticoController extends BaseController
{
    //Se crean las variables a utilizar de los respectivos servicios
    protected $productoService;

    /*Creacion del constructor para evitar llamar al servicio en cada funcion*/
    public function __construct()
    {
        //Se instancian nuevos objetos de los servicios
        $this->productoService = new ProductoFarmaceuticoService();
    }

    //Metodo que realiza la eliminacion de un producto farmaceutico
    public function bajaProducto($idProducto)
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

    /*
    use App\Services\MedicamentoService;
    use App\Services\TipoProductoService;
    use App\Services\MedidaProductoService;
    protected $medicamentoService;
    protected $tipoProductoService;
    protected $medidaProductoService;
    $this->medicamentoService = new MedicamentoService();
    $this->tipoProductoService = new TipoProductoService();
    $this->medidaProductoService = new MedidaProductoService();
    
    
    Metodo que permite la modificacion/edificion de los productos farmaceuticos
    public function edit(int $id)
    {
        $productoModel = model('App\Models\ProductoFarmaceuticoModel');
        $producto = $productoModel->obtenerProductoPorId($id);
        
        if (!$producto) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->request->getMethod() === 'post') {
            try {
                $this->productoService->modificarProductoFarmaceutico($id, $this->request->getPost());
                return redirect()->to('/productos')->with('success', 'Producto actualizado.');
            } catch (\InvalidArgumentException $e) {
                $errors = json_decode($e->getMessage(), true) ?? ['error' => $e->getMessage()];
                return redirect()->back()->withInput()->with('errors', $errors);
            }
        }
        //Se llaman a los servicios que contienen los metodos para la carga de los dropdowns
        $data['producto'] = $producto;
        $data['medicamentos'] = $this->medicamentoService->obtenerMedicamentosDropdown();
        $data['tipos'] = $this->tipoProductoService->obtenerTiposDropdown();
        $data['medidas'] = $this->medidaProductoService->obtenerMedidaDropdown();
        return view('productos/edit', $data);
    }
            //Metodo para la creacion de un nuevo producto farmaceutico que hace uso de los servicios
    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            try {
                $id = $this->productoService->crearProducto($this->request->getPost());
                return redirect()->to('/productos')->with('success', 'Producto creado correctamente.');
            } catch (\InvalidArgumentException $e) {
                $errors = json_decode($e->getMessage(), true) ?? ['error' => $e->getMessage()];
                return redirect()->back()->withInput()->with('errors', $errors);
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Error al crear el producto.');
            }
        }

        //Se llaman a los servicios que contienen los metodos para la carga de los dropdowns
        $data['medicamentos'] = $this->medicamentoService->obtenerMedicamentosDropdown();
        $data['tipos'] = $this->tipoProductoService->obtenerTiposDropdown();
        $data['medidas'] = $this->medidaProductoService->obtenerMedidaDropdown();
        return view('productos/create', $data);
    }*/
}