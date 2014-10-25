<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
//Iniciamos archivos
require 'loader.php';
$loader = new LoaderDb();
$loader->run();
//-------------------

//$pos = Connections::get('system') -> select() -> from('parametros') -> fetchRow();

#echo '<pre>' . print_r($pos, 1) . '</pre>';

$correos = Connections::get('system');# -> select() -> from('parametros') -> fetchAll();

echo '<pre>' . print_r($correos->update('parametros',array('name' => 'algo', 'otra' => 'qwqw'), 'id = 1'), 1) . '</pre>';
