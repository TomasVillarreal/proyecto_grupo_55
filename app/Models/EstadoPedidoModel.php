<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\EstadoPedido;

class EstadoPedidoModel extends Model
{
    protected $table = 'estado_pedido';//Nuestra tabla en la bd
    protected $primaryKey = 'id_estado_pedido';
    protected $allowedFields = ['tipo_estado_pedido'];
    protected $useTimestamps = false; //Para evitar guardar y asignar fechas automaticamente
    
    // Funcion que crea un objeto de la entidad EstadoPedido.
    private function crearObjeto(array $registro): EstadoPedido
    {
        return new EstadoPedido(
            (int) $registro['id_estado_pedido'],
            $registro['tipo_estado_pedido'],
        );
    }

    /* Funcion que obtiene todos los registros de la BD que son de la clase estado_pedido
    y para cada uno de estos registros va creando un objeto de la entidad EstadoPedido*/
    public function obtenerTodos(): array
    {
        $registros = $this->orderBy('tipo_estado_pedido', 'ASC')->findAll();

        return array_map(fn($r) => $this->crearObjeto($r), $registros);
    }


    /*Se crea un método para obtener todos los estados de los pedidos ordenados alfabeticamente,
    lo cual va a ser útil para cargar en el listado de los pedidos.
    Asocia a cada ID su nombre de estado y es más fácil llamarlo en los formualrios
    ahorrando lógica en un controlador
    
    public function obtenerParaDropdown(): array
    {
        $estados = $this->orderBy('tipo_estado_pedido', 'ASC')->findAll();
        $opciones_estados = [];
        foreach ($estados as $estado) {
            $opciones_estados[$estado->id_estado_pedido] = $estado->tipo_estado_pedido;
        }
        return $opciones_estados;
    }*/
}