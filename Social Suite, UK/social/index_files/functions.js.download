/* Show More */
function show_more(start, search, page, selector, showmore) {
	/* Post and get response */
	$.post(page, 'limit='+start+'&search='+search, function(data) {

		if($.trim(data) == "") {
			/* If no response, fadeOut the button */
			$(showmore).fadeOut('slow');

		} else {
			/* Remove the current show more button */
			$(showmore).remove();

			/* Append the result to the div */
			$(data).hide().appendTo(selector).fadeIn('slow');

			/* Refresh the bootstrap tooltip */
            $('[data-toggle="tooltip"]').tooltip()
		}
	});
}

function notify_data(data) {
    var parsed = JSON.parse(data);

    if(parsed.message) {
        if(parsed.message.constructor === Array) {
            for(var i = 0; i <= parsed.message.length; i++) {
                $.notify(parsed.message[i], {
                    className: parsed.status,
                    clickToHide: true,
                    autoHide: true,
                    autoHideDelay: 5000
                });
            }
        }
    }

    return parsed;
}

$.fn.extend({
    animateCss: function(animationName, callback) {
        var animationEnd = (function(el) {
            var animations = {
                animation: 'animationend',
                OAnimation: 'oAnimationEnd',
                MozAnimation: 'mozAnimationEnd',
                WebkitAnimation: 'webkitAnimationEnd',
            };

            for (var t in animations) {
                if (el.style[t] !== undefined) {
                    return animations[t];
                }
            }
        })(document.createElement('div'));

        this.addClass('animated ' + animationName).one(animationEnd, function() {
            $(this).removeClass('animated ' + animationName);

            if (typeof callback === 'function') callback();
        });

        return this;
    },
});