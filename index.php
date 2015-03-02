<?php

ini_set('error_reporting', E_ALL | E_NOTICE | E_STRICT);
ini_set('display_errors', '1');
ini_set('track_errors', 'On');

require 'loader.php';

//Iniciamos archivos
$loader = new AdminDB();
$loader->run();
//-------------------
/*$sql = Connections::get()
            ->select(array('a.*','x.path'))
            ->from(array('a' => 'actividades'))
            ->innerJoin(array('x' => 'imagenes_actividades'), 'a.imagen_predeterminada = x.id_imagen_actividad')
            ->where('a.promocion = ?',1)
            ->where('a.status IN(?)', array(1,2));*/

$sql = Connections::get()->select()->from('agenda');

try {
    echo '<pre>' . print_r(Fetch::all($sql),1) .'</pre>';
} catch (Exception $e) {
    echo $e->getMessage();
}