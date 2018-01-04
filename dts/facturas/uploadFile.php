<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$data = array();

if (isset($_GET['files'])) {
    $error = false;
    $files = array();

    $uploaddir = 'files/';
    foreach ($_FILES as $file) {
        if (move_uploaded_file($file['tmp_name'], $uploaddir . basename('products.xlsx'))) {
            $files[] = $uploaddir . $file['name'];
        } else {
            $error = true;
        }
    }
    $data = ($error) ? array('error' => 'There was an error uploading your files') : array('files' => $files);
} else {
    $data = array('success' => 'Form was submitted', 'formData' => $_POST);
}


if (isset($_GET['lg'])) {

    $error = false;
    $files = array();

    $uploaddir = 'files/';
    foreach ($_FILES as $file) {

        $info = new SplFileInfo($file['name']);

        if (strcmp($info->getExtension(), 'jpg') === 0) {

            if (move_uploaded_file($file['tmp_name'], $uploaddir . basename('logo.' . $info->getExtension()))) {
                $files[] = $uploaddir . $file['name'];
            } else {
                $error = true;
                $msg = "Ocurrio un problema con la carga del archivo.";
            }
        } else {
            $error = true;
            $msg = "Tipo de archivo no valido";
        }
    }
    $data = ($error) ? array('error' => $msg) : array('files' => $files);
}

echo json_encode($data);
