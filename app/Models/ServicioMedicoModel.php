<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\ServicioMedico;

class ServicioMedicoModel extends Model
{
    protected $table = 'servicio_medico';
    protected $primaryKey = 'id_servicio_medico';
    protected $allowedFields = ['nombre_servicio_medico'];
    protected $useTimestamps = false; //Para evitar guardar y asignar fechas automaticamente
    
    private function crearObjeto(array $registro): ServicioMedico
    {
        return new ServicioMedico(
            (int) $registro['id_servicio_medico'],
            $registro['nombre_servicio_medico'],
        );
    }

    public function obtenerTodos(): array
    {
        $registros = $this->orderBy('nombre_servicio_medico', 'ASC')->findAll();

        return array_map(fn($r) => $this->crearObjeto($r), $registros);
    }

    /*Se crea un método para obtener todos los servicios medicos que pueden realizar pedidos ordenados alfabeticamente,
    lo cual va a ser útil para cargar en el formulario de listado y creacion de pedidos.
    Asocia a cada ID su nombre de servicio y es más fácil llamarlo en los formualrios
    ahorrando lógica en un controlador
    
    public function obtenerParaDropdown(): array
    {
        $servicios = $this->orderBy('nombre_servicio_medico', 'ASC')->findAll();
        $opciones_servicios = [];
        foreach ($servicios as $servicio) {
            $opciones_servicios[$servicio->id_servicio_medico] = $servicio->nombre_servicio_medico;
        }
        return $opciones_servicios;
    }*/
}