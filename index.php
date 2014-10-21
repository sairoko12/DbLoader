<?php

//Iniciamos archivos
require 'loader.php';
$loader = new LoaderDb();
$loader->run();
//-------------------

$pos = Connections::get('system') -> select() -> from('parametros') -> fetchRow();

echo '<pre>' . print_r($pos, 1) . '</pre>';

$correos = Connections::get('agenda') -> select() -> from('correos') -> fetchAll();

echo '<pre>' . print_r($correos, 1) . '</pre>';
