<?php

namespace App\Controllers;

use App\Services\ProductoFarmaceuticoService;
use App\Services\MedicamentoService;
use App\Services\TipoProductoService;
use App\Services\MedidaProductoService;

class Productos extends BaseController
{
    //Se crean las variables a utilizar de los respectivos servicios
    protected $productoService;
    protected $medicamentoService;
    protected $tipoProductoService;
    protected $medidaProductoService;

    /*Creacion del constructor para evitar llamar al servicio en cada funcion*/
    public function __construct()
    {
        //Se instancian nuevos objetos de los servicios
        $this->productoService = new ProductoFarmaceuticoService();
        $this->medicamentoService = new MedicamentoService();
        $this->tipoProductoService = new TipoProductoService();
        $this->medidaProductoService = new MedidaProductoService();
    }

    //Se crea este metodo para usar en la vista
    public function index()
    {
        $productoModel = model('App\Models\ProductoFarmaceuticoModel');
        $data['productos'] = $productoModel->obtenerProductosConRelaciones();
        $data['formateador'] = $this->productoService; 
        return view('productos/index', $data);
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
    }

    //Metodo que permite la modificacion/edificion de los productos farmaceuticos
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

    //Metodo que realiza la eliminacion de un producto farmaceutico
    public function delete(int $id)
    {
        try {
            $this->productoService->eliminarProducto($id);
            return redirect()->to('/productos')->with('success', 'Producto eliminado.');
        } catch (\Exception $e) {
            return redirect()->to('/productos')->with('error', $e->getMessage());
        }
    }
}