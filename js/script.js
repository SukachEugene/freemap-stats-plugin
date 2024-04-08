;
(function ($) {
    
    $(document).on('ready', function () {

        $('.freemap-stats-loader-container').css('opacity', '0');
        $('.freemap-stats-loader-container').css('z-index', '-1');


        $('.freemap-stats-hide-all').click(function() {
            $('.freemap-stats-show-all').removeClass('active');
            $(this).addClass('active');
            $('.freemap-stats-li-group').hide();
            $('.freemap-stats-list-selector').attr('data-state', 'show');
            $('.freemap-stats-list-selector').addClass('show');
            $('.freemap-stats-list-selector').html('SHOW LIST');
        });

        $('.freemap-stats-show-all').click(function() {
            $('.freemap-stats-hide-all').removeClass('active');
            $(this).addClass('active');
            $('.freemap-stats-li-group').show();
            $('.freemap-stats-list-selector').attr('data-state', 'hide');
            $('.freemap-stats-list-selector').removeClass('show');
            $('.freemap-stats-list-selector').html('HIDE LIST');
        });

        $('.freemap-stats-list-selector').click(function() {
            if ($(this).attr('data-state') == 'hide') {
                $(this).parent().next('.freemap-stats-li-group').hide();
                $(this).attr('data-state', 'show');
                $(this).addClass('show');
                $(this).html('SHOW LIST');
            } else {
                $(this).parent().next('.freemap-stats-li-group').show();
                $(this).attr('data-state', 'hide');
                $(this).removeClass('show');
                $(this).html('HIDE LIST');
            }
           
        });





        $(window).on('orientationchange', function () {

        });

    });
    

    $(window).on('load', function () {
        
    });

    $(window).on('resize', function () {

    });


    $(window).on('scroll', function () {

    });
    
    
    $(window).on('afterChange', function () {

    });
    

}(jQuery));