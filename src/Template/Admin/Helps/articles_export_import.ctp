<style>
#help {
    margin-bottom: 500px;
}
#help .page-header {
    font-size: 26px;
    color: #fff;
    margin-bottom: 15px;
}
#help .section {
    background-color: #fff !important;
    padding: 15px;
}
#help h1 {
    color: #337ab7;
}
#help p {
    font-size: 20px;
}
#help li {
    font-size: 20px;
    list-style-type: upper-roman;
}
</style>

<div class="container-fluid" id="help">
    <div class="row">
        <div class="col-12 col-sm-12 col-lg-12">
            <h1><i class="fa fa-question-circle"></i> Come aggiornare un listino già esistente?</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-3 col-sm-12 col-lg-3">
            <div data-spy="affix" data-offset-top="0">
                <div class="list-group">
                    <a class="list-group-item active" href="#step-0">Video tutorial</a>
                    <a class="list-group-item" href="#step-1">Scaricare il listino</a>
                    <a class="list-group-item" href="#step-2">Modificare l'excel</a>
                    <a class="list-group-item" href="#step-3">Caricare l'excel</a>
                </div>
            </div>
        </div>
        <div class="col-9 col-sm-12 col-lg-9">
            <div id="step-0" class="section">
                <h1 class="page-header">Video tutorial</h1>
                <p>
                    <iframe width="950" height="500" src="https://www.youtube.com/embed/W1K5ZAu0MYc?si=bQ27VaJUxlzUpTaQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                </p>
            </div>
            <div id="step-1" class="section">
                <h1 class="page-header">Scaricare il listino del produttore</h1>
                <p>
                Scaricare il listino dalla pagina <a href="/admin/articles/export" target="_blank">esporta</a>, 
                <ul>
                    <li>selezionare il <b>produttore</b></li>
                    <li>tra i <b>campi esportabili</b> ci sarà l'<b>identificativo</b> dell'articolo</li>
                </ul>
                <a title="clicca per ingrandire l'immagine" class="img_orig" href="" data-toggle="modal" data-target="#modalImg">
                    <img class="img-responsive" src="/img/helps/articles-export-identificativo.jpg" /></a>
                </p>
                <p>
                    L'identificativo dell'articolo è quel codice numerico che identifica in maniera univoca un determinato articolo: 
                        quindi <b>non</b> dev'essere modificato 

                </p>
            </div>
            <div id="step-2" class="section">
                <h1 class="page-header">Modificare l'excel esportato</h1>
                <p>
                Modificare l'excel esportato stando attenti a non modificare la colonna dell'<b>identificativo</b> dell'articolo
                </p>
                <a title="clicca per ingrandire l'immagine" class="img_orig" href="" data-toggle="modal" data-target="#modalImg">
                    <img class="img-responsive" src="/img/helps/articles-export-excel-identificativo.jpg" /></a>
            </div>
            <div id="step-3" class="section">
                <h1 class="page-header">Caricare l'excel modificato</h1>
                <p style="background-color: #fff !important">
                Caricare l'excel modificato dalla pagina <a href="/admin/articles/import" target="_blank">importa</a>
                <ul>
                    <li>selezionando il <b>produttore</b></li>
                    <li>
                        escludere la prima riga dell'excel perchè riguarda l'<b>intestazione</b> delle colonne, <br />
                        cliccando sul bottone <br />
                        <a class="btn-block btn btn-danger"><span>La prima riga è l'intestazione, non verrà considerata</span></a>
                    </li>
                    <li>caricare l'excel</li>
                    <li>
                        come <b>prima colonna</b> scegliere <b>identificativo articolo</b>
                        <a title="clicca per ingrandire l'immagine" class="img_orig" href="" data-toggle="modal" data-target="#modalImg">
                            <img class="img-responsive" src="/img/helps/articles-import-colonna-identificativo.jpg" /></a>
                    </li>
                    <li>
                        stabilire l'associazione di tutte le altre colonne
                    </li>
                    <li>importare i dati</li>
                </ul>
                </p>
            </div>
        </div>
    </div>
</div>	  <!-- container -->


<div id="modalImg" class="modal fade">
 <div class="modal-dialog modal-lg">
  <div class="modal-content">
   <!-- div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Dettaglio immagine</h4>
   </div -->
   <div class="modal-body" style="overflow: auto;">
    <p><img src="" id="modalImgOrig" /></p>
   </div>
   <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
   </div>
  </div>
 </div>
</div>

<?php
$js = "
$( function() {

	$('.img_orig').click(function () {
		var src = $(this).find('img').attr('src');
    	console.log(src);
    	$('#modalImgOrig').attr('src', src);
  	})

    var offset = 0;
    
    // Function to handle affix width and classes in affix menu on page loading, scrolling or resizing 
    function affix() {
        
        // Fit affix width to its parent's width
        var affixElement = $('div[data-spy=\"affix\"]');
        affixElement.width(affixElement.parent().width());

        // Position of vertical scrollbar 
        var position = $(window).scrollTop();
        if (position >= offset) {
            $('.wrapper .section').each(function(i) {
                // Current content block's position less body padding
                var current = $(this).offset().top - offset - 1;
                
                // Add active class to corresponding affix menu while removing the same from siblings as per position) of current block
                if (current <= position) {
                    $('a', affixElement).eq(i).addClass('active').siblings().removeClass('active');
                }
            });
        } else {
            $('a', affixElement).find('.active').removeClass('active').end().find(':first').addClass('active');
        }
    };
  
    // Call to function on DOM ready
    affix();
    
    // Call on scroll or resize
  	$(window).on('scroll resize', function() {
        affix();
    });

    // Smooth scrolling at click on nav menu item
    $('a[href*=#]:not([href=#])').click(function() {
        var target = $(this.hash);
        $('html,body').animate({
            scrollTop: target.offset().top - offset
        }, 500);
        return false;
    });
});";
$this->Html->scriptBlock($js, ['block' => true]);