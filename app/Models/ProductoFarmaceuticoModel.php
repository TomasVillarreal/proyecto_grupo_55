<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\ProductoFarmaceutico;
use App\Entities\Medicamento;
use App\Entities\TipoProducto;
use App\Entities\MedidaProducto;

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
    
    // Funcion que crea un objeto de la entidad ProductoFarmaceutico.
    private function crearObjeto(array $r): ProductoFarmaceutico
    {
        $medicamento = new Medicamento(
            (int) $r['id_medicamento'],
            $r['nombre_medicamento'],
            (bool) $r['activo_medicamento'] ?? true
        );

        $tipo = new TipoProducto(
            (int) $r['id_tipo_producto'],
            $r['nombre_tipo_producto']
        );

        $medida = new MedidaProducto(
            (int) $r['id_medida_producto'],
            $r['nombre_medida']
        );

        return new ProductoFarmaceutico(
            (int) $r['id_producto'],
            $r['descripcion_producto'],
            (float) $r['dosis_producto'],
            $medicamento,
            $tipo,
            $medida,
            (bool) $r['activo_producto']
        );
    }

    /* Funcion que obtiene todos los productos farmacéuticos cargados en el sistema
    También se obtienen con los JOINs necesarios para ver el resto de caracteristicas de otras
    tablas y realizar la creacion de todos los objetos farmaceuticos*/
    public function obtenerTodos(bool $producto_activo = false): array
    {
        $builder = $this->db->table('producto_farmaceutico pf');

        $builder->select('
            pf.*,
            m.id_medicamento,
            m.nombre_medicamento,
            m.activo_medicamento,
            tp.id_tipo_producto,
            tp.nombre_tipo_producto,
            mp.id_medida_producto,
            mp.nombre_medida
        ');

        $builder->join('medicamento m', 'm.id_medicamento = pf.id_medicamento');
        $builder->join('tipo_producto tp', 'tp.id_tipo_producto = pf.id_tipo_producto');
        $builder->join('medida_producto mp', 'mp.id_medida_producto = pf.id_medida_producto');

        if (!$producto_activo) {
            $builder->where('pf.activo_producto', 1);
            $builder->where('m.activo_medicamento', 1);
        }

        $builder->orderBy('m.nombre_medicamento', 'ASC');
        $builder->orderBy('pf.dosis_producto', 'ASC');

        $result = $builder->get()->getResultArray();

        return array_map(fn($r) => $this->crearObjeto($r), $result);
    }

    /* Funcion que obtiene los productos farmacéuticos de un medicamento en específico, usado en el main.js.
    Obtiene tambien  todos los ids y datos necesarios para poder crear todos los objetos.*/
    public function obtenerPorMedicamento(int $idMedicamento): array
    {
        $builder = $this->db->table('producto_farmaceutico pf');

        $builder->select('
            pf.*,
            m.id_medicamento,
            m.nombre_medicamento,
            m.activo_medicamento,
            tp.id_tipo_producto,
            tp.nombre_tipo_producto,
            mp.id_medida_producto,
            mp.nombre_medida
        ');

        $builder->join('medicamento m', 'm.id_medicamento = pf.id_medicamento');
        $builder->join('tipo_producto tp', 'tp.id_tipo_producto = pf.id_tipo_producto');
        $builder->join('medida_producto mp', 'mp.id_medida_producto = pf.id_medida_producto');

        $builder->where('pf.id_medicamento', $idMedicamento);
        $builder->where('pf.activo_producto', 1);

        $result = $builder->get()->getResultArray();

        return array_map(fn($r) => $this->crearObjeto($r), $result);
    }

    /*Se crea un método que obtiene los productos farmacéuticos de un medicamento en específico, usado en el main.js.
    Obtiene tambien  todos los ids y datos necesarios para poder crear todos los objetos.*/
    public function buscarProductoExistente(array $data): ?ProductoFarmaceutico
    {
        $registro = $this->where('id_medicamento', (int) $data['id_medicamento'])
            ->where('id_tipo_producto', (int) $data['id_tipo_producto'])
            ->where('id_medida_producto', (int) $data['id_medida_producto'])
            ->where('dosis_producto', $data['dosis_producto'])
            ->first();

        if (!$registro) {
            return null;
        }

        return $this->crearObjeto($registro);
    }

    /* Funcion que devuelve un unico producto farmaceutico, tal que el medicamento devuelto sera aquel
    cuyo id sea igual al id pasado como argumento*/
    public function buscarProductoPorID(int $id): ?ProductoFarmaceutico
    {
        $registro = $this->find($id);

        if (!$registro) {
            return null;
        }

        return $this->crearObjeto($registro);
    }

    /* Funcion que cambia el activo_producto del objeto cuyo id sea igual al 
    pasado como parametro a 1 (es decir, activo)*/
    public function reactivar(int $id) : bool
    {
        return $this->update($id, [
            'activo_producto' => 1
        ]);
    }

    /* Funcion que cambia el activo_producto del objeto cuyo id sea igual al 
    pasado como parametro a 0 (es decir, activo)*/
    public function desactivar(int $id) : bool
    {
        return $this->update($id, [
            'activo_producto' => 0
        ]);
    }

    /* Funcion que desactiva a todos los productos farmaceuticos que estan asociados al
    medicamento cuyo id sea igual al pasado como parametro*/
    public function desactivarPorMedicamento(int $idMedicamento) : bool {
        return $this->where('id_medicamento', $idMedicamento)->set(['activo_producto' => 0])->update();
    }

    /* Funcion que modifica al producto cuyo id coincida con el argumento, con los datos
    pasados en el array*/
    public function modificar(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /* Funcion que agrega un nuevo producto con los datos pasados como parametros, y retorna
    el id del nuevo producto creado */
    public function agregar(array $data): int
    {
        $this->insert($data);
        return (int) $this->getInsertID();
    }

    /* Funcion que verifica la existencia de algun medicamento con el nombre pasado como parametro
    Si encuentra algun medicamento, devuelve false, y sino devuelve true.*/
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
    }
}