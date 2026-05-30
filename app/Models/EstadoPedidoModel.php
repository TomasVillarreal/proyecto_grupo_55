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
    
    private ?array $cacheEstados = null;

    public function obtenerTodos(): array
    {
        if ($this->cacheMedidas !== null) {
            return $this->cacheMedidas;
        }

        $registros = $this->orderBy('tipo_estado_pedido', 'ASC')->findAll();

        $tipos = [];

        foreach ($registros as $registro) {
            $tipos[] = new EstadoPedido (
                $registro['id_estado_pedido'],
                $registro['tipo_estado_pedido']
            );
        }

        $this->cacheEstados = $tipos;

        return $this->cacheEstados;
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