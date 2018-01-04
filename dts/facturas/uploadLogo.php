<?php
if (isset($_FILES['archivo'])) {
    $archivo = $_FILES['archivo'];
    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $time = 'logo';
    $nombre = "$time.$extension";
    if (move_uploaded_file($archivo['tmp_name'],"files/$nombre")) {
        echo 1;
    } else {
        echo 0;
    }
}
?>
