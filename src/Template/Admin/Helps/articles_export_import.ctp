<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-sm-12 col-lg-12">
            <h1>Come aggiornare un listino già esistente?</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-3 col-sm-3 col-lg-3">
            <div data-spy="affix" data-offset-top="0">
                <div class="list-group">
                    <a class="list-group-item active" href="#step-1">Step1</a>
                    <a class="list-group-item" href="#step-2">Step2</a>
                    <a class="list-group-item" href="#step-3">Step3</a>
                </div>
            </div>
        </div>
        <div class="col-9 col-sm-9 col-lg-9">
            <div id="step-1" class="section">
                <h1 class="page-header">Step1</h1>
                <p>
                Lorem Ipsum è un testo segnaposto utilizzato nel settore della tipografia e della stampa. Lorem Ipsum è considerato il testo segnaposto standard sin dal sedicesimo secolo, quando un anonimo tipografo prese una cassetta di caratteri e li assemblò per preparare un testo campione. È sopravvissuto non solo a più di cinque secoli, ma anche al passaggio alla videoimpaginazione, pervenendoci sostanzialmente inalterato. Fu reso popolare, negli anni ’60, con la diffusione dei fogli di caratteri trasferibili “Letraset”, che contenevano passaggi del Lorem Ipsum, e più recentemente da software di impaginazione come Aldus PageMaker, che includeva versioni del Lorem Ipsum.
                </p>
            </div>
            <div id="step-2" class="section">
                <h1 class="page-header">Step2</h1>
                <p>
                Lorem Ipsum è un testo segnaposto utilizzato nel settore della tipografia e della stampa. Lorem Ipsum è considerato il testo segnaposto standard sin dal sedicesimo secolo, quando un anonimo tipografo prese una cassetta di caratteri e li assemblò per preparare un testo campione. È sopravvissuto non solo a più di cinque secoli, ma anche al passaggio alla videoimpaginazione, pervenendoci sostanzialmente inalterato. Fu reso popolare, negli anni ’60, con la diffusione dei fogli di caratteri trasferibili “Letraset”, che contenevano passaggi del Lorem Ipsum, e più recentemente da software di impaginazione come Aldus PageMaker, che includeva versioni del Lorem Ipsum.
                </p>
            </div>
            <div id="step-3" class="section">
                <h1 class="page-header">Step3</h1>
                <p>
                Lorem Ipsum è un testo segnaposto utilizzato nel settore della tipografia e della stampa. Lorem Ipsum è considerato il testo segnaposto standard sin dal sedicesimo secolo, quando un anonimo tipografo prese una cassetta di caratteri e li assemblò per preparare un testo campione. È sopravvissuto non solo a più di cinque secoli, ma anche al passaggio alla videoimpaginazione, pervenendoci sostanzialmente inalterato. Fu reso popolare, negli anni ’60, con la diffusione dei fogli di caratteri trasferibili “Letraset”, che contenevano passaggi del Lorem Ipsum, e più recentemente da software di impaginazione come Aldus PageMaker, che includeva versioni del Lorem Ipsum.
                </p>
            </div>
        </div>
    </div>
</div>	  <!-- container -->
<?php
$js = "
$( function() {
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