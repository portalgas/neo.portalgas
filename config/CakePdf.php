<?php
/*
 * https://github.com/FriendsOfCake/CakePdf
 * engine => CakePdf.WkHtmlToPdf, CakePdf.DomPdf 
 */
return [
    'CakePdf' => [
        'engine' => [
            'className' => 'CakePdf.Dompdf',
            'options' => [
                'print-media-type' => false,
                'outline' => true,
                'dpi' => 96
            ]
        ],
        //'binary' => '/usr/local/bin/wkhtmltopdf',
        //'cwd' => '/tmp',
        'margin' => [
            'bottom' => 15,
            'left' => 50,
            'right' => 30,
            'top' => 45
        ],
        'pageSize' => 'Letter',
        'orientation' => 'landscape', // portrait
        'download' => true
    ]
];