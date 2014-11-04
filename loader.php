<?php

define('PATH_FILE_CONFIG','config');
define('FILE_CONFIG','config.json');
# This optional set
# define('TYPE_FILE_CONFIG','json');

class AdminDB {
    public function run() {
        $file = file_get_contents(PATH_FILE_CONFIG . '/' . FILE_CONFIG);
        $data = json_decode($file, 1);
        
        $files_load = $data['loader_class'];
        
        foreach ($files_load AS $dir => $files) {
            foreach ($files AS $file) {
                $this->load($file, $dir);
            }
        }
        
        Connections::register_connections();
    }
    
    
    
    public function load($file, $dir) {
        $file = $dir . '/' . strtolower($file) . '.php';
        if (@file_exists($file)) {
            require_once $file;
        } else {
            throw new Exception('Not found ' . $file, 500);
        }
    }
}
