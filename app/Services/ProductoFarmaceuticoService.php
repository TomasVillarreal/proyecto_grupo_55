<?php

namespace App\Services;

use App\Models\ProductoFarmaceuticoModel;
use App\Models\TipoProductoModel;
use App\Models\MedidaProductoModel;
use App\Models\MedicamentoModel;

class ProductoFarmaceuticoService
{
    //Variables a utilizar que hacen referencia a cada modelo usado
    protected ProductoFarmaceuticoModel $productoModel;
    protected TipoProductoModel $tipoProductoModel;
    protected MedidaProductoModel $medidaProductoModel;
    protected MedicamentoModel $medicamentoModel;
    protected MedicamentoService $medicamentoService;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion.*/
    public function __construct()
    {
        //Se reconocen e instancian las clases de los modelos a utilizar
        $this->productoModel = new ProductoFarmaceuticoModel();
        $this->tipoProductoModel = new TipoProductoModel();
        $this->medidaProductoModel = new MedidaProductoModel();
        $this->medicamentoModel = new MedicamentoModel();
        $this->medicamentoService = new MedicamentoService();
    }

    /*Se crea un metodo que valida las dosis de acuerdo a nuestras
    reglas de negocio. 
    Devuelve null si cumple con las validaciones y en caso contrario
    devuelve un string con el error*/
    public function validarDosis(float $dosis): ?string
    {
        if (!is_numeric($dosis)) {
            return "La dosis debe ser un número";
        }
        
        //Se castea a float y se valida que sea mayor a 0 y menor 3000
        $dosis = (float) $dosis;
        if ($dosis <= 0 || $dosis > 3000) {
            return "La dosis debe estar entre 0.01 y 3000";
        }
        
        return null;
    }

    /*Se crea un metodo que valida las posibles descripciones de los productos
    de acuerdo a nuestras reglas de negocio.  */
    public function validarDescripcion(?string $descripcion): ?string
    {
        //Valida si el campo está vacío
        if (empty($descripcion)) {
            return null; // Es opcional
        }
        
        //preg_match controla que no se ingresen al final cosas como #, ^, palabra y muchos espacios y un nro,etc
        if (!preg_match('/^[A-Za-z0-9ÁÉÍÓÚáéíóúñÑ(%-]+([ ,.()%-]+[A-Za-z0-9ÁÉÍÓÚáéíóúñÑ]+)*[).%]*$/u', $descripcion)) {
            return "La descripción contiene caracteres no permitidos (#, ^,etc)";
        }
        
        return null;
    }

    /*Se crea un metodo que valida que el producto farmaceutico en su totalidad
    cumple con las validaciones establecidas.
    En caso de que si cumpla, retorna un array vacio, en caso contrario, retorna
    un array con los errores. */
    public function validarProductoFarmaceutico(array $data, ?int $excludeId = null): array
    {
        //Se crea el array que contendrá los errores, o no-
        $errors = [];

        //Se validan las dosis haciendo uso del metodo anterior
        $dosisCheck = $this->validarDosis($data['dosis_producto'] ?? '');
        if ($dosisCheck !== null) {
            $errors['dosis_producto'] = $dosisCheck;
        }

        //También se valida la despcriones haciendo uso del metodo anterior
        $descripcionCheck = $this->validarDescripcion($data['descripcion_producto'] ?? null);
        if ($descripcionCheck !== null) {
            $errors['descripcion_producto'] = $descripcionCheck;
        }

        //Se valida que el producto entero no tenga duplicados
        if (empty($errors)) {
            if ($this->productoModel->productoFarmaceuticoUnico(
                (float) $data['dosis_producto'],
                (int) $data['id_medicamento'],
                (int) $data['id_tipo_producto'],
                (int) $data['id_medida_producto'],
                $excludeId
            )) {
                $errors['unique'] = 'Ya existe un producto con la misma combinación de medicamento, dosis, medida y tipo.';
            }
        }
        return $errors;
    }

    // Metodo que prepara los datos a insertar / actualizar
    private function prepararDatosProducto(array $data) : array {
        return [
            'id_medicamento' => (int) $data['id_medicamento'],
            'id_tipo_producto' => (int) $data['id_tipo_producto'],
            'id_medida_producto' => (int) $data['id_medida_producto'],
            'dosis_producto' => (float) $data['dosis_producto'],
            'descripcion_producto' => empty($data['descripcion_producto']) ? null : trim($data['descripcion_producto']),
        ];
    }

    /*Se crea un método para crear un nuevo producto farmaceutico, teniendo en cuenta que 
    cumple con las validaciones. Retorna el id del nuevo producto*/
    public function crearProducto(array $data): int
    {
        //Manejo de errores como ya se vió en otros procedimientos
        $errors = $this->validarProductoFarmaceutico($data);
        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode(' ', $errors));//Transforma el array en texto formato JSON
        }
        
        $productoExistente = $this->productoModel->buscarProductoExistente($data);
        //Si el producto existe y está activo, lanza un error
        if($productoExistente !== null){
            if($productoExistente->obtenerActivo() === true){
                throw new \InvalidArgumentException("Producto farmaceutico ya ingresado!");
            }
            //si esta desactivado, lo reactiva y devuelve el id de ese producto reactivado
            $this->productoModel->reactivar($productoExistente->obtenerID());
            return (int) $productoExistente->obtenerID();
        }

        //Si no hay error de unicidad, es porque el producto es nuevo, por lo que se preparo los datos del nuevo prod
        $insertData = array_merge($this->prepararDatosProducto($data), ['activo_producto'=>1]);

        // Inserto el nuevo prod con los datos y devuelvo el id.
        $resultado = $this->productoModel->agregar($insertData);
        if($resultado === 0){
            throw new \RuntimeException('No se pudo crear el medicamento');
        }
        return (int) $resultado;
    }

    /* Metodo que verifica que se hayan producido cambios entre el producto
    pasado como argumento y el array de datos pasado como argumento */
    private function verificarCambiosProducto(object $producto, array $data) : bool{
        return $producto->id_medicamento === $data['id_medicamento'] &&
            $producto->id_tipo_producto === $data['id_tipo_producto'] &&
            $producto->id_medida_producto === $data['id_medida_producto'] &&
            (float)$producto->dosis_producto === (float)$data['dosis_producto'] &&
            ($producto->descripcion_producto ?? null) === $data['descripcion_producto'];
    }

    /*Metodo para actualizar/modeificar un producto farmaceutico */
    public function modificarProductoFarmaceutico(int $idProductoFarmaceutico, array $data): bool
    {
        //Se asigna a la variable el producto que se busca en el modelo. En caso de no existir, mensaje de error
        $producto = $this->productoModel->buscarProductoPorID($idProductoFarmaceutico);
        if ($producto === null) {
            throw new \InvalidArgumentException('El producto no existe.');
        }
        
        //Se toman los valores recibidos y se almacenan para su posterior comparacion
        $updateData = $this->prepararDatosProducto($data);

        
        //Se comparan valores recibidos con valores en bd para determinar si hubo cambios
        if ($this->verificarCambiosProducto($producto, $updateData)){
            // Si entra aca no hubo cambios
            return false;
        }

        // validacion de los datos obtenidos
        $errors = $this->validarProductoFarmaceutico($updateData, $idProductoFarmaceutico);
        // si hay errorres, tiro excepcion
        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode(' ', $errors));
        }

        //si no hay errores, modifico el producto
        return $this->productoModel->modificar($idProductoFarmaceutico, $updateData);
    }

    /*Metodo para la eliminación lógica del medicamento haciendo uso del metodo de su modelo */
    public function eliminarProducto(int $idProductoFarmaceutico): bool
    {
        $producto = $this->productoModel->buscarProductoPorID($idProductoFarmaceutico);

        if ($producto === null || !$producto->obtenerActivo()) {
            throw new \InvalidArgumentException("El producto no existe o ya está inactivo.");
        }

        return $this->productoModel->desactivar($producto->obtenerID());
    }

    /* Funcion que elimina a un medicamento cuyo id coicida con el pasado por parametro
    y que elimine tmb todos los productos asociados a ese medicamento*/
    public function eliminarConMedicamento(int $idMedicamento) : void {
        $eliminarMed = $this->medicamentoService->eliminarMedicamento($idMedicamento);
        $eliminarProds = $this->productoModel->desactivarPorMedicamento($idMedicamento);
    }

    /*Metodo que va a ser utilizado para cargar dinamicamente los campos del producto
    farmaceutico una vez se seleccione un medicamento en la vista de UPDATE*/
    public function obtenerProductosPorMedicamento(int $idMedicamento): array
    {
        $productos = $this->productoModel->obtenerPorMedicamento($idMedicamento);
        $listadoProductos = [];//array que contendrá todos los productos de un medicamento

        foreach ($productos as $producto) {
            $listadoProductos[] = [
                'id_producto' => $producto->obtenerID(),
                'dosis_producto' => $producto->obtenerDosis(),
                'descripcion_producto' => $producto->obtenerDescripcion(),
                'id_tipo_producto' => $producto->obtenerTipo()->obtenerID(),
                'id_medida_producto' => $producto->obtenerUnidadMedida()->obtenerID(),
                'nombre_tipo_producto' => $producto->obtenerTipo()->obtenerNombre(),
                'nombre_medida' => $producto->obtenerUnidadMedida()->obtenerNombre(),
            ];
        }
        return $listadoProductos;
    }   
}