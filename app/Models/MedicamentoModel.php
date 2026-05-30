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

    private function crearObjeto(array $registro): Medicamento
    {
        return new Medicamento(
            (int) $registro['id_medicamento'],
            $registro['nombre_medicamento'],
            (bool) $registro['activo_medicamento']
        );
    }

    public function obtenerTodos(): array
    {
        $registros = $this->where('activo_medicamento', 1)
            ->orderBy('nombre_medicamento', 'ASC')
            ->findAll();

        return array_map(fn($r) => $this->crearObjeto($r), $registros);
    }

    public function obtenerPorID(int $id): ?Medicamento
    {
        $registro = $this->find($id);

        if (!$registro) {
            return null;
        }

        return $this->crearObjeto($registro);
    }

    public function obtenerPorNombre(string $nombre): ?Medicamento
    {
        $registro = $this->where('nombre_medicamento', $nombre)
            ->first();

        if (!$registro) {
            return null;
        }

        return $this->crearObjeto($registro);
    }

    public function reactivar(Medicamento $med): bool
    {
        $med->cambiarActivo(true);

        return $this->update($med->obtenerID(), [
            'activo_medicamento' => 1
        ]);
    }

    public function desactivar(Medicamento $med): bool
    {
        $med->cambiarActivo(false);

        return $this->update($med->obtenerID(), [
            'activo_medicamento' => 0
        ]);
    }

    public function modificar(int $id, string $nombre): bool
    {
        return $this->update($id, [
            'nombre_medicamento' => $nombre
        ]);
    }

    public function agregar(string $nombre): int
    {
        $this->insert([
            'nombre_medicamento' => $nombre,
            'activo_medicamento' => 1
        ]);

        return (int) $this->getInsertID();
    }

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