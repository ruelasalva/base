$(function() {
    var Accordion = function(el, multiple) {
        this.el = el || {};
        // more then one submenu open?
        this.multiple = multiple || false;

        var dropdownlink = this.el.find('.dropdownlink');
        dropdownlink.on('click',
        { el: this.el, multiple: this.multiple },
        this.dropdown);
    };

    Accordion.prototype.dropdown = function(e) {
        var $el = e.data.el,
        $this = $(this),
        //this is the ul.submenuItems
        $next = $this.next();

        $next.slideToggle();
        $this.parent().toggleClass('open');

        if(!e.data.multiple) {
            //show only one menu at the same time
            $el.find('.submenuItems').not($next).slideUp().parent().removeClass('open');
        }
    }

    var accordion = new Accordion($('.accordion-menu'), false);


    /* ======================================
    FILTERS
    ========================================= */
    function filters()
    {
    	var url_current = $('#url-current').data('url');
    	var url_temp    = '?';
    	var option      = '';
    	var order       = $('#order-by').val();

    	url_temp += 'orden=' + order + '&';

    	window.location = url_current + url_temp.slice(0, -1);
    }

    /*===============================================
	SI CAMBIA UN SELECT
	===============================================*/
	$('#order-by').change(function(){
		filters();
	});
})
