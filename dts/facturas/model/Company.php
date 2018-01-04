<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Company {
    
    private $id;
    private $razon;
    private $direccion;
    private $telefono;
    private $rif;
    
    
    function getId() {
        return $this->id;
    }

    function getRazon() {
        return $this->razon;
    }

    function getDireccion() {
        return $this->direccion;
    }

    function getTelefono() {
        return $this->telefono;
    }

    function getRif() {
        return $this->rif;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setRazon($razon) {
        $this->razon = $razon;
    }

    function setDireccion($direccion) {
        $this->direccion = $direccion;
    }

    function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    function setRif($rif) {
        $this->rif = $rif;
    }


    
    
}