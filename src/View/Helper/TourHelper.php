<?php

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\StringTemplateTrait;
use Cake\View\Helper\HtmlHelper;
use Cake\View\Helper\FormHelper;
use Cake\Core\Configure;
use Cake\View\Helper\BreadcrumbsHelper;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;
use Cake\Log\Log;

class TourHelper extends Helper {

    public $helpers = ['Html', 'Form'];
    
    public function beforeRender() {
    }

    public function render($jsonTours) {

		if(!Configure::read('tour.active'))
			return;

    	if(empty($jsonTours))
    		return;

		$js = "$(function() { 
					console.log('tour');

					tour = new Tour({
					  steps: ".$jsonTours.",
					  debug: false,
					  //storage: true,
					  autoscroll: true,
					  keyboard: true,
					  template: \"<div class='popover tour' style='min-width:350px;'> \
									<div class='arrow'></div> \
									<h3 class='popover-title'></h3> \
									<div class='popover-content'></div> \
									<div class='popover-navigation'> \
										<div class='btn-group'> \
											<button class='btn btn-primary' data-role='prev'>« Precedente</button> \
											<button class='btn btn-primary' data-role='next'>Prossimo »</button> \
										</div> \
										<button class='btn btn-success' data-role='end'>Fine tour</button> \
									</div> \
									</div>\",
						afterGetState: function (key, value) {},
						afterSetState: function (key, value) {
							// console.log('tour afterSetState '+key+'  '+value);
							if(key=='tour_end')
								toogleTreeMenuTour(true); 
							else
							if(key=='tour_current_step')
								toogleTreeMenuTour(false); 
						},
						afterRemoveState: function (key, value) {},
						onHide: function (tour) {},
						onHidden: function (tour) {},

					});
					tour.init();
					tour.start();

					console.log('tour.ended() '+tour.ended());
					if(tour.ended())	
						toogleTreeMenuTour(true); 

				});

				var tour = null;
				
				function treeMenuTourAction() {

					// console.log('treeMenuTourAction');
					// console.log(tour);

					if(tour !== null)
						tour.restart();	
				}
				function toogleTreeMenuTour(active) {
					
					// console.log('toogleTreeMenuTour');
					// console.log(tour);
					// console.log(tourDataTables);

					if(tour === null && tourDataTables === null)
						$('.treetour').hide();
					else {
						$('.treetour').show();

						if(active) {
							// console.log('toogleTreeMenuTour ACTIVE');
							$('#click-treetour').click(function() {
							  	$(this).on('click', treeMenuTourAction);
							});
							$('#click-treetour').css('cursor', 'pointer');
							$('.treetour').css('opacity', '0.9');
						}
						else {
							// console.log('toogleTreeMenuTour DISACTIVE');
							$('#click-treetour').click(function() {
							  	$(this).off('click', treeMenuTourAction);
							});	
							$('#click-treetour').css('cursor', 'default');
							$('.treetour').css('opacity', '0.2');	
						}		
					}
				}				
			";

		$this->Html->script('bootstrap-tour/bootstrap-tour.js');	
		$this->Html->scriptBlock($js, ['block' => true]);
    }
}