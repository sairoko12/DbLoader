<?php

class LoaderDb {
    private $file_config = "config.json";
    private $files_load;
    
    public function run() {
        $file = file_get_contents($this->file_config);
        $data = json_decode($file, 1);
        
        $this->files_load = $data['loader_files'];
        
        foreach ($this->files_load AS $dir => $files){
            foreach ($files AS $file) {
                $this->import($dir . '/' . $file);
            }
        }
        
        Connections::register_connections();
    }
    
    public function import($filename) {
        $file = $filename . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
}