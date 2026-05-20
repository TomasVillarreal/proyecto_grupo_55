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
    protected $medicamentoService;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion.*/
    public function __construct()
    {
        //Se reconocen e instancian las clases de los modelos a utilizar
        $this->productoModel = model(ProductoFarmaceuticoModel::class);
        $this->tipoProductoModel = model(TipoProductoModel::class);
        $this->medidaProductoModel = model(MedidaProductoModel::class);
        $this->medicamentoModel = model(MedicamentoModel::class);
        $this->medicamentoService = new MedicamentoService();

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

    /*Se crea un método para crear un nuevo producto farmaceutico, teniendo en cuenta que 
    cumple con las validaciones. Retorna el id del nuevo producto*/
    public function crearProducto(array $data): int
    {
        //Se busca primero si el producto ya existe (activo o inactivo da igual)
        $productoExistente = $this->productoModel
            ->where('id_medicamento', (int) $data['id_medicamento'])
            ->where('id_tipo_producto', (int) $data['id_tipo_producto'])
            ->where('id_medida_producto', (int) $data['id_medida_producto'])
            ->where('dosis_producto', $data['dosis_producto'])
            ->first();

        //Si el producto existe y está activo, lanza un error
        if ($productoExistente && $productoExistente->activo_producto == 1) {
            throw new \InvalidArgumentException("Producto farmaceutico ya ingresado!");
        }

        //Si el producto existe pero está inactivo, se reactiva
        if ($productoExistente && $productoExistente->activo_producto == 0) {
            $this->productoModel->update($productoExistente->id_producto,['activo_producto' => 1]);
            return (int) $productoExistente->id_producto;
        }

        //Manejo de errores como ya se vió en otros procedimientos
        $errors = $this->validarProductoFarmaceutico($data);
        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode(' ', $errors));//Transforma el array en texto formato JSON
        }

        //Si no hay error de unicidad, es porque el producto es nuevo, por lo que se inserta el nuevo producto farmaceutico
        $insertData = [
            'id_medicamento' => (int) $data['id_medicamento'],
            'id_tipo_producto' => (int) $data['id_tipo_producto'],
            'id_medida_producto' => (int) $data['id_medida_producto'],
            'dosis_producto' => (float) $data['dosis_producto'],
            'descripcion_producto' => empty($data['descripcion_producto']) ? null : trim($data['descripcion_producto']),
            'activo_producto' => 1,
        ];

        //Se asigna el nuevo id
        $id = $this->productoModel->insert($insertData);
        
        //Se maneja un posible error en la inserción
        if (!$id) {
            throw new DatabaseException('No se pudo crear el producto.');
        }

        //Retorna el nuevo id
        return $id;
    }
    
    //Se crea un metodo que recibe los datos del POST para la creacion de un medicamento, a través de su servicio
    //y luego crea el producto a través de su propio servicio.
    public function crearProductoCompleto(int|string $idMedicamentoPost, ?string $nombreMedicamentoPost, array $productoDataPost)
    {
        $db = \Config\Database::connect();//Se crea la conexión con la BD
        $db->transBegin();//Comienza la transaccion

        try {
            //Se determina mediante un if (a modo de filtro) si el ID del medicamento es nuevo o es para un nuevo producto farmaceutico.
            if ($idMedicamentoPost === 'new') {
                if (empty($nombreMedicamentoPost)) {
                    throw new \InvalidArgumentException("Debe ingresar el nombre del medicamento");
                }

                //Llamamos al servicio ed medicamento para la creacion del mismo
                $idMedicamentoNuevo = $this->medicamentoService->crearMedicamento($nombreMedicamentoPost);

            } else {
                $idMedicamentoNuevo = (int) $idMedicamentoPost;//El medicamento no es nuevo si no que fue seleccionado del dropdown
                $this->validarMedicamentoActivo($idMedicamentoNuevo);//Se realiza una validacion llamando al model de medicamentos
            }

            //Se crea el nuevo producto con el id del medicamento asignado
            $productoDataPost['id_medicamento'] = $idMedicamentoNuevo;
            $this->crearProducto($productoDataPost);//Se hace uso del metodo que antes ya creaba el producto

            $db->transCommit();//Se finaliza la transaccion
        } catch (\Exception $e) {
            $db->transRollback();
            throw $e; //Re-lanzamos para que el controlador la atrape
        }
    }

    //Metodo que realiza la validacion de un medicamento para saber si esta aactivo o no.
    //Hace uso del model de medicamento
    private function validarMedicamentoActivo(int $id)
    {
        $medicamento = $this->medicamentoModel->find($id);
        if (!$medicamento || !$medicamento->activo_medicamento) {
            throw new \InvalidArgumentException('El medicamento seleccionado no es válido o está inactivo.');
        }
    }

    /*Metodo para actualizar/modeificar un producto farmaceutico */
    public function modificarProductoFarmaceutico(int $idProductoFarmaceutico, array $data): bool
    {
        //Se asigna a la variable el producto que se busca en el modelo. En caso de no existir, mensaje de error
        $producto = $this->productoModel->find($idProductoFarmaceutico);
        if (!$producto) {
            throw new \InvalidArgumentException('El producto no existe.');
        }
        
        //Se toman los valores recibidos y se almacenan para su posterior comparacion
        $updateData = [
            'id_medicamento' => (int) $data['id_medicamento'],
            'id_tipo_producto' => (int) $data['id_tipo_producto'],
            'id_medida_producto' => (int) $data['id_medida_producto'],
            'dosis_producto' => (float) $data['dosis_producto'],
            'descripcion_producto' => empty($data['descripcion_producto']) ? null : trim($data['descripcion_producto']),
        ];
        
        //Se comparan valores recibidos con valores en bd para determinar si hubo cambios
        if ($producto->id_medicamento == $updateData['id_medicamento'] &&
            $producto->id_tipo_producto == $updateData['id_tipo_producto'] &&
            $producto->id_medida_producto == $updateData['id_medida_producto'] &&
            (float)$producto->dosis_producto == (float)$updateData['dosis_producto'] &&
            ($producto->descripcion_producto ?? null) == $updateData['descripcion_producto']){
            return false;
        }

        $errors = $this->validarProductoFarmaceutico($data, $idProductoFarmaceutico);
        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode(' ', $errors));
        }

        //Se guarda la informacion del producto farmaceutico actualizado
        $updateData = [
            'id_medicamento' => (int) $data['id_medicamento'],
            'id_tipo_producto' => (int) $data['id_tipo_producto'],
            'id_medida_producto' => (int) $data['id_medida_producto'],
            'dosis_producto' => (float) $data['dosis_producto'],
            'descripcion_producto' => empty($data['descripcion_producto']) ? null : trim($data['descripcion_producto']),
        ];

        return $this->productoModel->update($idProductoFarmaceutico, $updateData);
    }

    /*Metodo para la eliminación lógica del medicamento haciendo uso del metodo de su modelo */
    public function eliminarProducto(int $idProductoFarmaceutico): void
    {
        $producto = $this->productoModel->find($idProductoFarmaceutico);

        if (!$producto || !$producto->activo_producto) {
            throw new \InvalidArgumentException("El producto no existe o ya está inactivo.");
        }

        $this->productoModel->update($idProductoFarmaceutico, ['activo_producto' => 0]);
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