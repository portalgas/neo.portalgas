<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use Cake\Controller\ComponentRegistry;

class ArticlesImportExportComponent extends Component {

    // campi della tabella
    private $_import_fields = [];
    // campi opzionali
    private $_export_source_fields = [];
    // campi esportati    
	private $_export_export_fields = [];
    // campi di default        
	private $_export_default_fields = [];

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
/*
        $this->_import_fields = [
            __('Code'),
            __('Note')
        ];*/
        $this->_import_fields = [
            'codice' => __('Code'),
            'nota' => __('Note'),
            'ingredienti' => 'Ingredienti',
            'um_riferimento' => __('um_riferimento'),
            'qta_minima' => __('qta_minima'),
            'qta_massima' => __('qta_massima'),
            'qta_minima_order' => __('qta_minima_order'),
            'qta_massima_order' => __('qta_massima_order'),
            'qta_multipli' => __('qta_multipli')
        ];
        $this->_export_source_fields = [
                                'codice' => ['label' => __('Code'), 'nota' => '001'],
                                'nota' => ['label' => __('Note'), 'nota' => "descrizione dell'articolo"],
                                'ingredienti' => ['label' => 'Ingredienti', 'nota' => "solo ingredienti naturali"],
                                'um_riferimento' => ['label' => __('um_riferimento'), 'nota' => 'Kg'],
                                'qta_minima' => ['label' => __('qta_minima'), 'nota' => '1'],
                                'qta_massima' => ['label' => __('qta_massima'), 'nota' => '10'],
                                'qta_minima_order' => ['label' => __('qta_minima_order'), 'nota' => '0'],
                                'qta_massima_order' => ['label' => __('qta_massima_order'), 'nota' => '0'],
                                'qta_multipli' => ['label' => __('qta_multipli'), 'nota' => '1']
                            ];

        $this->_export_export_fields = [
            'name' => ['label' => __('Name'), 'nota' => 'Toma valle di Lanzo'],
            'prezzo' => ['label' => __('Price'), 'nota' => '12,50'],
            'qta' => ['label' => __('qta'), 'nota' => '500'],
            'um' => ['label' => __('UM'), 'nota' => 'Gr'],
            'pezzi_confezione' => ['label' => __('pezzi_confezione'), 'nota' => '1'],
            'bio' => ['label' => __('Bio'), 'nota' => 'Si'],
            'flag_presente_articlesorders' => ['label' => __('flag_presente_articlesorders'), 'nota' => 'Si']
        ];   
        
        $this->_export_default_fields = ['id' => ['label' => 'Identificativo articolo', 'nota' => 'Necessario se si vuole aggiornare l\'articolo']];
    }

    /* 
     * campi import 
     */
    public function getImportFields($user, $debug=false) {
        return $this->_import_fields;
	}

    /* 
     * campi opzionali
     */   
    public function getExportSourceFields($user, $debug=false) {
        return $this->_export_source_fields;
	}

	public function getExportFields($user, $debug=false) {
        return $this->_export_export_fields;
	}

	public function getExportDefaultFields($user, $debug=false) {
        return $this->_export_default_fields;
	}

    public function export($user, $request, $articles, $debug=false) {

        $supplier_organization_id = $request['supplier_organization_id'];
        $request_export_fields = $request['export_fields'];
        
        /* 
            * campi da estrarre
            * aggiungo quelli di default (ex id)
            */
        $request_default_fields = [];
        foreach($this->_export_default_fields as $key => $default_field) {
            $request_default_fields[] = $key;
        }
            
        $arr_export_fields = [];
        $arr_export_fields = $request_default_fields;
        if(strpos($request_export_fields, ';')===false)
            $arr_export_fields[] = $request_export_fields;
        else
            $arr_export_fields = array_merge($arr_export_fields, explode(';', $request_export_fields));
        if($debug) debug($arr_export_fields);
        
        $alphabet = range('A', 'Z');
        // if($debug) debug($alphabet);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        /* 
            * header
            */
        foreach($arr_export_fields as $numResult => $arr_export_field) {
            $numCol = $alphabet[$numResult].'1';
            $sheet->setCellValue($numCol, __($arr_export_field));
        }

        foreach($articles as $numResult => $article) {

            $numRow = ($numResult + 2);

            foreach($arr_export_fields as $numResult2 => $arr_export_field) {
                $numCol = $alphabet[$numResult2].$numRow;
                $value = $article->{$arr_export_field};
                switch($value) {
                    case 'Y':
                        $value = 'Si';
                    break;
                    case 'N':
                        $value = 'No';
                    break;
                }
                if($debug) debug($numCol.' '.$arr_export_field.' '.$value);
                $sheet->setCellValue($numCol, $value);
            } // foreach($arr_export_fields as $numResult2 => $arr_export_field) 
        } // foreach($articles as $numResult => $article)

        $writer = new Xlsx($spreadsheet);
        return $writer;
    }
    
    public function read($file_path) {
        $results = false;
        if(!file_exists($file_path))
            return $results;

        $reader = new XlsxReader();
        $spreadsheet = $reader->load($file_path);
        $worksheet = $spreadsheet->getActiveSheet();
        $results = $worksheet->toArray();
        return $results;
    }
}