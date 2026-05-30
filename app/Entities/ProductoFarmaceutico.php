<?php

namespace App\Entities;

class ProductoFarmaceutico 
{
    private int $id;
    private string $nombre;
    private ?string $descripcion;
    private Medicamento $medicamento;
    private TipoProducto $tipo;
    private MedidaProducto $unidad_medida;
    private bool $activo;

    public function __construct(int $id, ?string $nombre, string $descripcion,
                                Medicamento $medicamento, TipoProducto $tipo, 
                                MedidaProducto $medida, bool $activo){
        $this->asignarID($id);
        $this->cambiarNombre($nombre);
        $this->cambiarDescripcion($descripcion);
        $this->asignarMedicamento($medicamento);
        $this->cambiarTipo($tipo);
        $this->cambiarUnidadMedida($medida);
        $this->cambiarActivo($activo);
    }

    private function asignarID(int $id) : void
    {
        $this->id = $id;
    }

    public function cambiarNombre(string $nombre) : void
    {
        $this->nombre = $nombre;
    }

    public function cambiarDescripcion(?string $desc) : void 
    {
        $this->descripcion = $desc;
    }

    private function asignarMedicamento(Medicamento $medicamento) : void
    {
        $this->medicamento = $medicamento;
    }

    public function cambiarTipo(TipoProducto $tipo) : void
    {
        $this->tipo = $tipo;
    }

    public function cambiarUnidadMedida(MedidaProducto $medida) : void
    {
        $this->unidad_medida = $medida;
    }

    public function cambiarActivo(bool $activo) : void
    {
        $this->activo = $activo;
    }

    public function obtenerID() : int
    {
        return $this->id;
    }

    public function obtenerNombre() : string
    {
        return $this->nombre;
    }

    public function obtenerDescripcion() : ?string 
    {
        return $this->descripcion;
    }

    public function obtenerMedicamento() : Medicamento
    {
        return $this->medicamento;
    }

    public function obtenerTipo() : TipoProducto
    {
        return $this->tipo;
    }

    public function obtenerUnidadMedida() : MedidaProducto
    {
        return $this->unidad_medida;
    }

    public function obtenerActivo() : bool
    {
        return $this->activo;
    }

}