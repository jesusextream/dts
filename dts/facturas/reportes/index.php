<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

	if (isset($_GET['f']) && isset($_GET['i'])){
            $tipo_fact = $_GET['f'];
            $invoice = $_GET['i'];
            $url = 'factura.php';
            ob_start();
            include $url;
            $html = ob_get_clean();
            echo $html;
        
        } else {
            header('location: ../');
            exit;
        }

