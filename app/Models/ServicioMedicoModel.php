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
    
    private ?array $cacheServiciosMedicos = null;

    public function obtenerTodos(): array
    {
        if ($this->cacheServiciosMedicos !== null) {
            return $this->cacheServiciosMedicos;
        }

        $registros = $this->orderBy('nombre_servicio_medico', 'ASC')->findAll();

        $tipos = [];

        foreach ($registros as $registro) {
            $tipos[] = new ServicioMedico(
                $registro['id_servicio_medico'],
                $registro['nombre_servicio_medico']
            );
        }

        $this->cacheServiciosMedicos = $tipos;

        return $this->cacheServiciosMedicos;
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