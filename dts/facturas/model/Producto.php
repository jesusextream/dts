<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Producto {
    private $cantidad;
    private $descripcion;
    private $precio;
    private $total;
    private $numPart;
    
    
    function getCantidad() {
        return $this->cantidad;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function getPrecio() {
        return $this->precio;
    }

    function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    function setPrecio($precio) {
        $this->precio = $precio;
    }
    
    function getTotal() {
        return $this->total;
    }

    function setTotal($total) {
        $this->total = $total;
    }
    
    function getNumPart() {
        return $this->numPart;
    }

    function setNumPart($numPart) {
        $this->numPart = $numPart;
    }

    
    

    function getArrayVars() {
        return array(
            'cant' => $this->cantidad,
            'numPart' => $this->numPart,
            'desc' => $this->descripcion,
            'prec' => $this->precio,
            'total' => number_format($this->total, 2, '.', ',')
        );
    }
    
}