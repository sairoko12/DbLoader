<?php

ini_set('error_reporting', E_ALL | E_NOTICE | E_STRICT);
ini_set('display_errors', '1');
ini_set('track_errors', 'On');


//Iniciamos archivos
require 'loader.php';
$loader = new AdminDB();
$loader->run();
//-------------------

echo '<pre>' . print_r(Connections::get('testing')->select()->from('correos')->where('id = 4')->fetchRow()->email,1) .'</pre>';
//echo '<pre>' . print_r(Connections::get('agenda')->select()->from('usuarios')->fetchAll(),1) .'</pre>';