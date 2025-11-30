(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();
    
    
    // Initiate the wowjs
    new WOW().init();


    // Sticky Navbar
    $(window).scroll(function () {
        if ($(this).scrollTop() > 90) {
            $('.nav-bar').addClass('fixed-top').css('padding', '0');
        } else {
            $('.nav-bar').removeClass('fixed-top').css('padding', '0px 90px');
        }
    });
    
    
    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });


    // Modal Video
    $(document).ready(function () {
        var $videoSrc;
        $('.btn-play').click(function () {
            $videoSrc = $(this).data("src");
        });
        console.log($videoSrc);

        $('#videoModal').on('shown.bs.modal', function (e) {
            $("#video").attr('src', $videoSrc + "?autoplay=1&amp;modestbranding=1&amp;showinfo=0");
        })

        $('#videoModal').on('hide.bs.modal', function (e) {
            $("#video").attr('src', $videoSrc);
        })
    });


    // Facts counter
    $('[data-toggle="counter-up"]').counterUp({
        delay: 10,
        time: 2000
    });


    // Donation progress
    $('.donation-item .donation-progress').waypoint(function () {
        $('.donation-item .progress .progress-bar').each(function () {
            $(this).css("height", $(this).attr("aria-valuenow") + '%');
        });
    }, {offset: '80%'});


    // Header carousel
    $(".header-carousel").owlCarousel({
        animateOut: 'rotateOutUpRight',
        animateIn: 'rotateInDownLeft',
        items: 1,
        autoplay: true,
        smartSpeed: 1000,
        dots: false,
        loop: true,
        nav : true,
        navText : [
            '<i class="bi bi-chevron-left"></i>',
            '<i class="bi bi-chevron-right"></i>'
        ]
    });


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        items: 1,
        autoplay: true,
        smartSpeed: 1000,
        animateIn: 'fadeIn',
        animateOut: 'fadeOut',
        dots: false,
        loop: true,
        nav: true,
        navText : [
            '<i class="bi bi-chevron-left"></i>',
            '<i class="bi bi-chevron-right"></i>'
        ]
    });

    // Contact Form Handler
    $('#contact-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $('#contact-submit-btn');
        var $messageDiv = $('#contact-form-message');
        var originalBtnText = $submitBtn.html();
        
        // Disable submit button
        $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');
        $messageDiv.addClass('d-none');
        
        // Get form data
        var formData = {
            name: $('#name').val(),
            email: $('#email').val(),
            subject: $('#subject').val(),
            message: $('#message').val()
        };
        
        // Send AJAX request
        $.ajax({
            url: 'process_email.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $messageDiv.removeClass('d-none alert-danger').addClass('alert-success').html(response.message);
                    $form[0].reset();
                } else {
                    $messageDiv.removeClass('d-none alert-success').addClass('alert-danger').html(response.message);
                }
            },
            error: function(xhr, status, error) {
                var errorMsg = 'Sorry, there was an error sending your message. Please try again later.';
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMsg = response.message;
                    }
                } catch(e) {}
                $messageDiv.removeClass('d-none alert-success').addClass('alert-danger').html(errorMsg);
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });

    // Newsletter Subscription Handler
    $('#newsletter-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $('#newsletter-submit-btn');
        var $messageDiv = $('#newsletter-message');
        var $emailInput = $('#newsletter-email');
        var originalBtnHtml = $submitBtn.html();
        
        // Disable submit button
        $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
        $messageDiv.addClass('d-none');
        
        // Get email
        var email = $emailInput.val();
        
        // Send AJAX request
        $.ajax({
            url: 'process_subscribe.php',
            type: 'POST',
            data: { email: email },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $messageDiv.removeClass('d-none alert-danger').addClass('alert-success').html(response.message);
                    $emailInput.val('');
                } else {
                    $messageDiv.removeClass('d-none alert-success').addClass('alert-danger').html(response.message);
                }
            },
            error: function(xhr, status, error) {
                var errorMsg = 'Sorry, there was an error processing your subscription. Please try again later.';
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMsg = response.message;
                    }
                } catch(e) {}
                $messageDiv.removeClass('d-none alert-success').addClass('alert-danger').html(errorMsg);
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html(originalBtnHtml);
            }
        });
    });

    
})(jQuery);

