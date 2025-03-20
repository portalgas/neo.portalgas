var vueCmsPages = null;

$(function () {

    var router = new VueRouter({
        mode: 'history',
        routes: []
    });

    var ico_spinner = 'fa-lg fa fa-spinner fa-spin';

    vueCmsPages = new Vue({
        router,
        el: '#vue-cms-pages',
        data: {
            errors: [],
            pages: null,
            is_found_pages: false
        },
        methods: {
            gets: function(e) {

                $('.run-pages').show();
                $('.run-pages .spinner').addClass(ico_spinner);

                axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
                axios.defaults.headers.common['X-CSRF-Token'] = $('meta[name="csrfToken"]').attr('content');

                axios.get('/admin/api/cms-pages/index')
                    .then(response => {
                        /* console.log(response.data); */
                        $('.run-pages .spinner').removeClass(ico_spinner);
                        this.is_found_pages = true;
                        this.pages = response.data.results;
                    })
                    .catch(error => {
                        $('.run-run-pages .spinner').removeClass(ico_spinner);
                        this.is_found_pages = false;
                        console.error("Error: " + error);
                    });
            },
        },
        mounted: function(){
            console.log('mounted vueCmsPages');
            this.gets();
        }
    });
});
