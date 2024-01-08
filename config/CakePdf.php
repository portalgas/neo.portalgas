<?php
/*
 * https://github.com/FriendsOfCake/CakePdf
 * engine => CakePdf.WkHtmlToPdf, CakePdf.DomPdf 
 */
return  [
    'CakePdf' => [
		'engine' => [
			'className' => 'CakePdf.DomPdf',
			'options' => [
				'encoding' => 'UTF-8',
				'print-media-type' => false,
				'outline' => true,
				'dpi' => 96,
				'chroot' => WWW_ROOT,
				'isPhpEnabled' => true
			]
		],        
        'options' => [
            'encoding' => 'UTF-8',
            'print-media-type' => false,
            'outline' => true,
            'dpi' => 96,
            'chroot' => WWW_ROOT,
            //'isRemoteEnabled' => true
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
        ],    
        'margin' => [
            'bottom' => 15,
            'left' => 25,
            'right' => 25,
            'top' => 45
        ],
        'pageSize' => 'Letter',
        'orientation' => 'portrait', // landscape (orizzontale) portrait (verticale)
        'defaultFont' => 'Calibri',
        'encoding' => 'UTF-8',
        'download' => true,
        'isHtml5ParserEnabled' => true,
        'isPhpEnabled' => true,
    ]
];