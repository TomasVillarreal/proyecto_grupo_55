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

    private array $cacheMedicamentos = [];
    private bool $cacheCompleta = false;

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
        if ($this->cacheCompleta) {
            return array_values($this->cacheMedicamentos);
        }

        $registros = $this->where('activo_medicamento', 1)
            ->orderBy('nombre_medicamento', 'ASC')
            ->findAll();

        $this->cacheMedicamentos = [];

        foreach ($registros as $registro) {
            $med = $this->crearObjeto($registro);
            $this->cacheMedicamentos[$med->obtenerID()] = $med;
        }

        $this->cacheCompleta = true;

        return array_values($this->cacheMedicamentos);
    }

    public function obtenerPorID(int $id): ?Medicamento
    {
        if (isset($this->cacheMedicamentos[$id])) {
            return $this->cacheMedicamentos[$id];
        }

        $registro = $this->find($id);

        if (!$registro) {
            return null;
        }

        $med = $this->crearObjeto($registro);

        $this->cacheMedicamentos[$id] = $med;

        return $med;
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

        $this->cacheMedicamentos[$med->obtenerID()] = $med;
        $this->cacheCompleta = false;

        return $this->update($med->obtenerID(), [
            'activo_medicamento' => 1
        ]);
    }

    public function desactivar(Medicamento $med): bool
    {
        $med->cambiarActivo(false);

        $this->cacheMedicamentos[$med->obtenerID()] = $med;
        $this->cacheCompleta = false;

        return $this->update($med->obtenerID(), [
            'activo_medicamento' => 0
        ]);
    }

    public function modificar(int $id, string $nombre): bool
    {
        $ok = $this->update($id, [
            'nombre_medicamento' => $nombre
        ]);

        if ($ok && isset($this->cacheMedicamentos[$id])) {
            $this->cacheMedicamentos[$id]->cambiarNombre($nombre);
        }

        $this->cacheCompleta = false;

        return $ok;
    }

    public function agregar(string $nombre): int
    {
        $this->insert([
            'nombre_medicamento' => $nombre,
            'activo_medicamento' => 1
        ]);

        $id = (int) $this->getInsertID();

        $this->cacheMedicamentos[$id] = new Medicamento($id, $nombre, true);
        $this->cacheCompleta = false;

        return $id;
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

    public function invalidarCache(): void
    {
        $this->cacheMedicamentos = [];
        $this->cacheCompleta = false;
    }
}