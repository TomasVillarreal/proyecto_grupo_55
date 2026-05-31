<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Medicamento;

class MedicamentoModel extends Model
{
    protected $table = 'medicamento';
    protected $primaryKey = 'id_medicamento';
    protected $allowedFields = ['nombre_medicamento','activo_medicamento'];
    protected $useTimestamps = false;

    // Funcion que crea un objeto de la entidad Medicamento.
    private function crearObjeto(array $registro): Medicamento
    {
        return new Medicamento(
            (int) $registro['id_medicamento'],
            $registro['nombre_medicamento'],
            (bool) $registro['activo_medicamento']
        );
    }

    /* Funcion que obtiene todos los registros de la BD que son de la clase medicamento
    y para cada uno de estos registros va creando un objeto de la entidad Medicamento*/
    public function obtenerTodos(): array
    {
        $registros = $this->where('activo_medicamento', 1)
            ->orderBy('nombre_medicamento', 'ASC')
            ->findAll();

        return array_map(fn($r) => $this->crearObjeto($r), $registros);
    }

    /* Funcion que devuelve un unico medicamento, tal que el medicamento devuelto sera aquel
    cuyo id sea igual al id pasado como argumento*/
    public function obtenerPorID(int $id): ?Medicamento
    {
        $registro = $this->find($id);

        if (!$registro) {
            return null;
        }

        return $this->crearObjeto($registro);
    }

    /* Funcion que devuelve un unico medicamento, tal que el medicamento devuelto sera aquel
    cuyo nombre sea igual al nombre pasado como argumento*/
    public function obtenerPorNombre(string $nombre): ?Medicamento
    {
        $registro = $this->where('nombre_medicamento', $nombre)
            ->first();

        if (!$registro) {
            return null;
        }

        return $this->crearObjeto($registro);
    }

    /* Funcion que cambia el activo_medicamento del objeto cuyo id sea igual al 
    pasado como parametro a 1 (es decir, activo)*/
    public function reactivar(int $id): bool
    {
        return $this->update($id, [
            'activo_medicamento' => 1
        ]);
    }

    /* Funcion que cambia el activo_medicamento del objeto cuyo id sea igual al 
    pasado como parametro a 0 (es decir, inactivo)*/
    public function desactivar(int $id): bool
    {
        return $this->update($id, [
            'activo_medicamento' => 0
        ]);
    }

    /* Funcion que cambia el nombre del objeto cuyo id sea igual al 
    pasado al string pasado como parametro*/
    public function modificar(int $id, string $nombre): bool
    {
        return $this->update($id, [
            'nombre_medicamento' => $nombre
        ]);
    }

    /* Funcion que inserta un nuevo objeto con el nombre pasado como parametro
    y que devuelve el id del nuevo medicamento creado*/
    public function agregar(string $nombre): int
    {
        $this->insert([
            'nombre_medicamento' => $nombre,
            'activo_medicamento' => 1
        ]);

        return (int) $this->getInsertID();
    }

    /* Funcion que verifica la existencia de algun medicamento con el nombre pasado como parametro
    Si encuentra algun medicamento, devuelve false, y sino devuelve true.*/
    public function medicamentoUnico(string $nombre, ?int $excludeId = null): bool
    {
        $builder = $this->builder()
            ->where('nombre_medicamento', $nombre)
            ->where('activo_medicamento', 1);

        if ($excludeId !== null) {
            $builder->where('id_medicamento !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }
}