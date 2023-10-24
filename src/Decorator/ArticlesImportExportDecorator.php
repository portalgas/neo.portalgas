<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;

class ArticlesImportExportDecorator  extends AppDecorator {
	
	public $serializableAttributes = null; // ['id', 'name'];
	public $results; 

    /*
     * converto per bio e flag_presente_articlesorders
     * elimino le colonne che sono tutte null
     */
    public function __construct($articles)
    { 
        /*
         * elimino le righe che sono tutte null
         */        
        foreach($articles as $num_row => $article) {
            $rows_null = true; // testo se tutta la riga e' null
            foreach($article as $num_col => $art) {
                if($art!==null) {
                    $rows_null = false;  
                }
            }
            if($rows_null) // tutta la riga null => la elimino
                unset($articles[$num_row]);
        }

        /*
         * testo le colonne che sono tutte null
         */         
        $cols_nulls = []; // testo se tutta la colonna e' null
        foreach($articles as $num_row => $article) {
            foreach($article as $num_col => $art) {
                if($art=='Y')
                    $articles[$num_row][$num_col] = 'si';
                if($art=='N')
                    $articles[$num_row][$num_col] = 'no';

                if($art===null) {
                    if(!isset($cols_nulls[$num_col]))
                        $cols_nulls[$num_col] = 0;

                    $cols_nulls[$num_col]++;
                }
            }
        }
       
        /*
         * elimino le colonne che sono tutte null
         */
        $delete_cols_null=false;
        if(!empty($cols_nulls)) {
            foreach($cols_nulls as $num_col => $cols_null) {
                if($cols_null==count($articles)) {
                    $delete_cols_null=true;
                    foreach($articles as $num_row => $article) {
                        unset($articles[$num_row][$num_col]);       
                    }
                }
            }
        }

       /*
         * se ho eliminato delle colonne null 
         * devo ridefinire gli indici delle colonne ci fosse un vuoto in mezzo (ex 1,2,4)
         */        
        $new_articles = [];
        if($delete_cols_null) {
            foreach($articles as $num_row => $article) {
                foreach($article as $art) {
                    $new_articles[$num_row][] = $art;
                }
            }
        }
        else
            $new_articles = $articles;
    
        $this->results = $new_articles;
    }

	function name() {
		return $this->results;
	}    
}