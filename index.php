<?php

require 'loader.php';

ini_set('error_reporting', E_ALL | E_NOTICE | E_STRICT);
ini_set('display_errors', '1');
ini_set('track_errors', 'On');


//Iniciamos archivos
$loader = new AdminDB();
$loader->run();
//-------------------


echo '<pre>' . print_r(Connections::get()->select()->from('proveedores')->where('id_proveedor = ?', 4)->orWhere('id_usuario like ?', 'que pedo')->where('otro_campo = ?',12)->assemble(),1) .'</pre>';