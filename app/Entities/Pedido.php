<?php

namespace App\Entities;

// Se hace uso de esto para poder usar el DateTime sin quilombos en las funciones.
use DateTime;

class Pedido 
{
    private int $id;
    private DateTime $fecha_solicitud; 
    private ?string $comentario;
    private ?string $motivo_rechazo;
    private EstadoPedido $estado;
    private ServicioMedico $servicio;
    private Usuario $usuario;

    public function __construct(int $id, DateTime $fecha,
                                ?string $comentario, ?string $motivo,
                                EstadoPedido $estado, ServicioMedico $servicio,
                                Usuario $usuario){
        $this->asignarID($id);
        $this->asignarFechaSolicitud($fecha);
        $this->asignarComentario($comentario);
        $this->cambiarMotivoRechazo($motivo);
        $this->cambiarEstado($estado);
        $this->asignarServicioMedico($servicio);
        $this->asignarUsuario($usuario);
    }

    private function asignarID(int $id) : void
    {
        $this->id = $id;
    }

    private function asignarFechaSolicitud (DateTime $fecha) : void 
    {
        $this->fecha_solicitud = $fecha;
    }

    private function asignarComentario(?string $comentario) : void
    {
        $this->comentario = $comentario;
    }

    public function cambiarMotivoRechazo(?string $motivo) : void
    {
        $this->motivo_rechazo = $motivo;
    }

    public function cambiarEstado(EstadoPedido $estado) : void 
    {
        $this->estado = $estado;
    }

    private function asignarServicioMedico(ServicioMedico $servicio) : void 
    {
        $this->servicio = $servicio;
    }

    private function asignarUsuario(Usuario $usuario): void
    {
        $this->usuario = $usuario;
    }

    public function obtenerID() : int
    {
        return $this->id;
    }

    public function obtenerFechaSolicitud() : DateTime
    {
        return $this->fecha_solicitud;
    }

    public function obtenerComentario() : ?string
    {
        return $this->comentario;
    }

    public function obtenerMotivoRechazo() : ?string
    {
        return $this->motivo_rechazo;
    }

    public function obtenerEstado() : EstadoPedido
    {
        return $this->estado;
    }

    public function obtenerServicioMedico() : ServicioMedico
    {
        return $this->servicio;
    }

    public function obtenerUsuario(): Usuario
    {
        return $this->usuario;
    }
}