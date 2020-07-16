<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Controller\ComponentRegistry;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

class FileSystemComponent extends Component {

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

    public function getFiles($path, $options=[], $debug=false) {

        $results = [];

        $path_full = WWW_ROOT.$path;
        //if($debug) debug('path_full '.$path_full);

        $dir = new Folder($path_full);
        // if($debug) debug($dir);
        $files = $dir->find('.*\.jpg');
        foreach ($files as $numResult => $file) {
            
            $file = new File($dir->pwd() . DS . $file);
            //if($debug) debug($file);

            $results[$numResult]['name'] = $file->name;
            $results[$numResult]['path'] = $file->path;
            $results[$numResult]['info'] = $file->info;

            // $contents = $file->read();
            $file->close(); 
        }

        if($debug) debug($results);

        return $results;
    }

}