<?php

namespace App\Entities;

class DetallePedido
{
    private int $id;
    private int $cantidad; 
    private Pedido $pedido;
    private Proveedor $proveedor;
    private ProductoFarmaceutico $producto;

    public function __construct(int $id, int $cantidad, Pedido $pedido, 
                                Proveedor $proveedor, ProductoFarmaceutico $producto){
        $this->asignarID($id);
        $this->asignarCantidad($cantidad);
        $this->asignarPedido($pedido);
        $this->asignarProveedor($proveedor);
        $this->asignarProducto($producto);
    }

    private function asignarID(int $id) : void
    {
        $this->id = $id;
    }

    private function asignarCantidad(int $cantidad) : void 
    {
        $this->cantidad = $cantidad;
    }

    private function asignarPedido (Pedido $pedido) : void
    {
        $this->pedido = $pedido;
    }

    private function asignarProveedor(Proveedor $proveedor) : void
    {
        $this->proveedor = $proveedor;
    }

    private function asignarProducto(ProductoFarmaceutico $prod) : void 
    {
        $this->producto = $prod;
    }

    public function obtenerID() : int
    {
        return $this->id;
    }

    public function obtenerCantidad() : int
    {
        return $this->cantidad;
    }

    public function obtenerPedido() : Pedido
    {
        return $this->pedido;
    }

    public function obtenerProveedor() : Proveedor
    {
        return $this->proveedor;
    }

    public function obtenerProducto() : ProductoFarmaceutico
    {
        return $this->producto;
    }
}