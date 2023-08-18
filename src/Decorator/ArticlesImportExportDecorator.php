<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;

class ArticlesImportExportDecorator  extends AppDecorator {
	
	public $serializableAttributes = null; // ['id', 'name'];
	public $results; 

    /*
     * converto per bio e flag_presente_articlesorders
     */
    public function __construct($articles)
    {
        $results = [];
        foreach($articles as $num_row => $article) {
            foreach($article as $num_col => $art) {
                if($art=='Y')
                    $articles[$num_row][$num_col] = 'si';
                if($art=='N')
                    $articles[$num_row][$num_col] = 'no';
            }
        }

        $this->results = $articles;
    }

	function name() {
		return $this->results;
	}    
}