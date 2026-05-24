<?php

namespace App\Services;

use App\Models\ProductoFarmaceuticoModel;
use App\Models\TipoProductoModel;
use App\Models\MedidaProductoModel;
use App\Models\MedicamentoModel;
use App\Services\MedicamentoService;
use CodeIgniter\Database\Exceptions\DatabaseException;

class ProductoFarmaceuticoService
{
    //Variables a utilizar que hacen referencia a cada modelo usado
    protected $productoModel;
    protected $tipoProductoModel;
    protected $medidaProductoModel;
    protected $medicamentoModel;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion.*/
    public function __construct()
    {
        //Se reconocen e instancian las clases de los modelos a utilizar
        $this->productoModel = model(ProductoFarmaceuticoModel::class);
        $this->tipoProductoModel = model(TipoProductoModel::class);
        $this->medidaProductoModel = model(MedidaProductoModel::class);
        $this->medicamentoModel = model(MedicamentoModel::class);

    }

    /*Se crea un metodo que valida las dosis de acuerdo a nuestras
    reglas de negocio. 
    Devuelve true si cumple con las validaciones y en caso contrario
    devuelve un string con el error*/
    public function validarDosis($dosis): string|true
    {
        if (!is_numeric($dosis)) {
            return "La dosis debe ser un número";
        }
        
        //Se castea a float y se valida que sea mayor a 0 y menor 3000
        $dosis = (float) $dosis;
        if ($dosis <= 0 || $dosis > 3000) {
            return "La dosis debe estar entre 0.01 y 3000";
        }
        
        return true;
    }

    /*Se crea un metodo que valida las posibles descripciones de los productos
    de acuerdo a nuestras reglas de negocio.  */
    public function validarDescripcion(?string $descripcion): string|true
    {
        //Valida si el campo está vacío
        if (empty($descripcion)) {
            return true; // Es opcional
        }
        
        //preg_match controla que no se ingresen al final cosas como #, ^, palabra y muchos espacios y un nro,etc
        if (!preg_match('/^[A-Za-z0-9ÁÉÍÓÚáéíóúñÑ(%-]+([ ,.()%-]+[A-Za-z0-9ÁÉÍÓÚáéíóúñÑ]+)*[).%]*$/u', $descripcion)) {
            return "La descripción contiene caracteres no permitidos (#, ^,etc)";
        }
        
        return true;
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
        if ($dosisCheck !== true) {
            $errors['dosis_producto'] = $dosisCheck;
        }

        //También se valida la despcriones haciendo uso del metodo anterior
        $descripcionCheck = $this->validarDescripcion($data['descripcion_producto'] ?? null);
        if ($descripcionCheck !== true) {
            $errors['descripcion_producto'] = $descripcionCheck;
        }

        //Se valida que el producto entero no tenga duplicados
        if (empty($errors)) {
            if ($this->productoModel->productoFarmaceuticoUnico(
                (int) $data['id_medicamento'],
                (float) $data['dosis_producto'],
                (int) $data['id_medida_producto'],
                (int) $data['id_tipo_producto'],
                $excludeId
            )) {
                $errors['unique'] = 'Ya existe un producto con la misma combinación de medicamento, dosis, medida y tipo.';
            }
        }
        return $errors;
    }

    private function insertarProductoFarmaceutico(array $data) : int
    {
        $id = $this->productoModel->insert($data);
        
        //Se maneja un posible error en la inserción
        if (!$id) {
            throw new DatabaseException('No se pudo crear el producto.');
        }

        //Retorna el nuevo id
        return $id;
    }

    private function buscarProductoExistente(array $data) : ?object{
        //Se busca primero si el producto ya existe (activo o inactivo da igual)
        return $this->productoModel
        ->where('id_medicamento', (int) $data['id_medicamento'])
        ->where('id_tipo_producto', (int) $data['id_tipo_producto'])
        ->where('id_medida_producto', (int) $data['id_medida_producto'])
        ->where('dosis_producto', $data['dosis_producto'])
        ->first();
    }

    private function reactivarProducto(object $producto) : void
    {
        $this->productoModel->update($producto->id_producto,['activo_producto' => 1]);
    }

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
        
        $productoExistente = $this->buscarProductoExistente($data);
        //Si el producto existe y está activo, lanza un error
        if($productoExistente){
            if($productoExistente->activo_producto == 1){
                throw new \InvalidArgumentException("Producto farmaceutico ya ingresado!");
            }else{
                $this->reactivarProducto($productoExistente);
                return (int) $productoExistente->id_producto;
            }

        }

        //Si no hay error de unicidad, es porque el producto es nuevo, por lo que se inserta el nuevo producto farmaceutico
        $insertData = array_merge($this->prepararDatosProducto($data), ['activo_producto'=>1]);

        //Se asigna el nuevo id
        return $this->insertarProductoFarmaceutico($insertData);
    }


    private function buscarProductoPorID(int $id) : ?object{
        return $this->productoModel->find($id);
    }

    private function modificarProd(int $id, array $data) : bool{
        return $this->productoModel->update($id, $data); 
    }

    private function verificarCambiosProducto(object $producto, array $data) : bool{
        return $producto->id_medicamento == $data['id_medicamento'] &&
            $producto->id_tipo_producto == $data['id_tipo_producto'] &&
            $producto->id_medida_producto == $data['id_medida_producto'] &&
            (float)$producto->dosis_producto == (float)$data['dosis_producto'] &&
            ($producto->descripcion_producto ?? null) == $data['descripcion_producto'];
    }

    /*Metodo para actualizar/modeificar un producto farmaceutico */
    public function modificarProductoFarmaceutico(int $idProductoFarmaceutico, array $data): bool
    {
        //Se asigna a la variable el producto que se busca en el modelo. En caso de no existir, mensaje de error
        $producto = $this->buscarProductoPorID($idProductoFarmaceutico);
        if (!$producto) {
            throw new \InvalidArgumentException('El producto no existe.');
        }
        
        //Se toman los valores recibidos y se almacenan para su posterior comparacion
        $updateData = $this->prepararDatosProducto($data);
        
        //Se comparan valores recibidos con valores en bd para determinar si hubo cambios
        if ($this->verificarCambiosProducto($producto, $updateData)){
            return false;
        }

        $errors = $this->validarProductoFarmaceutico($data, $idProductoFarmaceutico);
        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode(' ', $errors));
        }

        return $this->modificarProd($idProductoFarmaceutico, $updateData);
    }

    public function eliminarProductosPorMedicamento(int $idMedicamento) : bool {
        return $this->productoModel->where('id_medicamento', $idMedicamento)->set(['activo_producto' => 0])->update();
    }

    private function desactivarUnProducto(int $id) : bool {
        return $this->productoModel->update($id, ['activo_producto' => 0]);
    }

    /*Metodo para la eliminación lógica del medicamento haciendo uso del metodo de su modelo */
    public function eliminarProducto(int $idProductoFarmaceutico): bool
    {
        $producto = $this->productoModel->find($idProductoFarmaceutico);

        if (!$producto || !$producto->activo_producto) {
            throw new \InvalidArgumentException("El producto no existe o ya está inactivo.");
        }

        return $this->desactivarUnProducto($idProductoFarmaceutico);
    }

    /*Metodo que va a ser utilizado para cargar dinamicamente los campos del producto
    farmaceutico una vez se seleccione un medicamento en la vista de UPDATE*/
    public function obtenerProductosPorMedicamento(int $idMedicamento): array
    {
        $productos = $this->productoModel->obtenerProductosPorMedicamento($idMedicamento);
        $listadoProductos = [];//array que contendrá todos los productos de un medicamento

        foreach ($productos as $producto) {
            $listadoProductos[] = [
                'id_producto' => $producto->id_producto,
                'dosis_producto' => $producto->dosis_producto,
                'descripcion_producto' => $producto->descripcion_producto,
                'id_tipo_producto' => $producto->id_tipo_producto,
                'id_medida_producto' => $producto->id_medida_producto,
                'nombre_tipo_producto' => $producto->nombre_tipo_producto,
                'nombre_medida' => $producto->nombre_medida,
            ];
        }
        return $listadoProductos;
    }


}