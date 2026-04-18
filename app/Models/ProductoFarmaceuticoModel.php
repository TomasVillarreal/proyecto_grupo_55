<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductoFarmaceuticoModel extends Model
{
    protected $table = 'producto_farmaceutico'; //Nuestra tabla en la bd
    protected $primaryKey = 'id_producto'; //Identificador único
    protected $allowedFields = [
        'descripcion_producto',
        'dosis_producto',
        'activo_producto',
        'id_medicamento',
        'id_tipo_producto',
        'id_medida_producto'
    ]; //Las columnas de la tabla
    protected $useTimestamps = false; //Para evitar guardar y asignar fechas automaticamente
    protected $returnType = 'object'; //Se especifica el tipo de dato a devolver

    /*Se crea un método para obtener todos los productos farmacéuticos cargados en el sistema
    También se obtienen con los JOINs necesarios para ver el resto de caracteristicas de otras
    tablas*/

    public function obtenerProductosFarmaceuticos(bool $producto_activo = false): array
    {
        $builder = $this->db->table('producto_farmaceutico pf');//Crea la consulta sobre la tabla especificada
        $builder->select('pf.*, m.nombre_medicamento, tp.nombre_tipo_producto, mp.nombre_medida');
        $builder->join('medicamento m', 'm.id_medicamento = pf.id_medicamento');//Se hace el JOIN con la tabla medicamento
        $builder->join('tipo_producto tp', 'tp.id_tipo_producto = pf.id_tipo_producto');//Se hace el JOIN con la tabla tipo producto
        $builder->join('medida_producto mp', 'mp.id_medida_producto = pf.id_medida_producto');//Se hace el JOIN con la tabla medida producto

        //Filtro para solo obtener productos activos que contengan medicamentos activos también
        if(!$producto_activo){
            $builder->where('pf.activo_producto',1);
            $builder->where('m.activo_medicamento',1);
        }

        /*Por una cuestión estética se busca que se devuelvan por nombre del medicamento
        ordenado alfabéticamente y por la dosis ordenada de menor a mayor*/

        $builder->orderBy('m.nombre_medicamento', 'ASC');
        $builder->orderBy('pf.dosis_producto', 'ASC');

        //Se obtienen los resultados
        return $builder->get()->getResult();
    }

    //Se crea un método que obtiene los productos farmacéuticos de un medicamento en específico
    public function obtenerProductosPorMedicamento(int $idMedicamento): array
    {
        $builder = $this->db->table('producto_farmaceutico pf');//Crea la consulta sobre la tabla especificada
        $builder->join('medicamento m', 'm.id_medicamento = pf.id_medicamento');//Se hace el JOIN con la tabla medicamento
        $builder->join('tipo_producto tp', 'tp.id_tipo_producto = pf.id_tipo_producto');//Se hace el JOIN con la tabla tipo producto
        $builder->join('medida_producto mp', 'mp.id_medida_producto = pf.id_medida_producto');//Se hace el JOIN con la tabla medida producto
        $builder->where('pf.id_medicamento', $idMedicamento);

        return $builder->get()->getResultArray();
    }

    //Se crea una función para la eliminación lógica de un producto farmacéutico
    
    public function eliminarProductoFarmaceutico(int $id):bool
    {
        return $this->update($id, ['activo_producto' => 0]);//Se modifica el campo activo del producto que se pasa por id.
    }

    /*Se crea una función para verificar si el producto farmacéutico es único.
    Su utilidad se va a dar en caso de que se quiera crear un nuevo producto farmacéutico,
    para evitar duplicados.*/

    public function productoFarmaceuticoUnico(float $dosis, int $idMedicamento, int $idTipo, int $idMedida, ?int $excludeId = null): bool
    {
        $builder = $this->builder();
        $builder->where('dosis_producto', $dosis)
                ->where('id_medicamento', $idMedicamento)
                ->where('id_tipo_producto', $idTipo)
                ->where('id_medida_producto', $idMedida)
                ->where('activo_producto', 1);
        
        /*Este if ayuda a que si en una modificación, por ejemplo, solo se modifica el nombre
        pero no la dosis ni medida, no lo tome como un nuevo medicamento*/
        if($excludeId != null){
            $builder->where('id_producto !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
        /*Booleano que verifica las coincidencias de lo solicitado en esta funcion y lo que hay en la BD
        En caso de devolver true indica que el producto farmaceutico ya existe, caso contrario con false.*/
    }
}