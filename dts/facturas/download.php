<?php

$enlace = "files/productsQBO.xlsx";
header ("Content-Disposition: attachment; filename=productsQBO.xlsx");
header ("Content-Type: application/octet-stream");
header ("Content-Length: ".filesize($enlace));
readfile($enlace)

?>