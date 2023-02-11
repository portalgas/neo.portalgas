"use strict";

var gasGroupsDeliveriesForm = null;

$(function () {
    
    var router = new VueRouter({
                mode: 'history',
                routes: []
            });

    gasGroupsDeliveriesForm = new Vue({
      router,
      el: '#vue-gas-groups_deliveries-form',
      data: {
        nota_evidenza_selected: 'NO',
        nota_evidenza: '',
        nota_evidenza_css: ''
      }, 
      methods: {
        init: function() {
          this.nota_evidenza_selected = $('select[name="nota_evidenza"]').val();
          // console.log(this.nota_evidenza_selected, 'init nota_evidenza_selected');
          this.nota_evidenza = $('#nota').val();
        }
      },       
      computed: {
        is_nota_evidenza() {
          if(this.nota_evidenza_selected!='NO') {
            if(this.nota_evidenza_selected=='MESSAGE')
              this.nota_evidenza_css = 'info';
            else 
            if(this.nota_evidenza_selected=='NOTICE')
              this.nota_evidenza_css = 'warning';
            else 
            if(this.nota_evidenza_selected=='ALERT')
              this.nota_evidenza_css = 'danger';
          
            return true;
          }
          else {
            this.nota_evidenza_css = '';
            return false;
          }
        }
      },
      created: function(){
        console.log('mounted gasGroupsDeliveriesForm');
        this.init();
      },
      filters: {
          html(text) {
            return text;
        },
      }      
    });
});