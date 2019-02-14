function pulse() {
    $('.game-card.active:visible').delay(200).fadeTo(1500, 0.6).delay(0).fadeTo(1500, 1.0, pulse);
};

function showGame(appId) {
    var selector = '#game' + appId;
    var selectedCard = '.game-card[data-app_id=' + appId + ']';

    $('#tv').fadeOut('fast');
    $(selector + ' img').attr('src', function () {
        return $(this).data('path_thumbnail')
    });		// loader
    $('#game-tabs').css('background-image', 'url(' + $(selector).attr('rel') + ')');		/// loader
    console.log(appId)
    if ($('.game-info:visible').length)
        $('.game-info:visible').fadeOut('fast', function () {
            $(selector).fadeIn('fast');
        });
    else
        $(selector).fadeIn('fast');

    $('#tv .carousel-indicators').empty();
    $('#tv .carousel-inner').empty();
    $(selector).find('.videos').children().each(function (index, video) {
// cant see them			$('#tv .carousel-indicators').append('<li data-target="#carouselExampleIndicators" data-slide-to="'+index+'" class="'+(index==0? 'active': '')+'"></li>');
        $('#tv .carousel-inner').append('<div class="carousel-item ' + (index == 0 ? 'active' : '') + '">\
				<video class="d-block w-100" controls preload="metadata" poster="' + $(video).data('thumbnail') + '">\
					<source src="' + $(video).data('video_480') + '" type="video/webm">\
					Your browser does not support the video tag.\
				</video>\
				<div class="carousel-caption d-none d-md-block">\
					<p>' + $(video).data('name') + '</p>\
				</div>\
			</div>');
    });
    $('.carousel-control-prev, .carousel-control-next').toggle($(selector).find('.videos').children().length > 1);

    setScreenshot($(selector).find('.thumbnails li:first img').data('path_thumbnail'), $(selector).find('.thumbnails li:first img').data('path_full'));
    $('#tv').fadeIn('fast');

    $('video').off('pause').on('pause', function () {
        console.log('pause');
        $('.carousel-caption p').fadeIn('fast');
    });

    $('video').off('play').on('play', function () {
        console.log('play');
        $('.carousel-caption p').fadeOut('fast');
    });

    $('.game-card').removeClass('active');
    $(selectedCard).addClass('active');
    $('#screenshot-modal .modal-title').text($(selectedCard).data('title'));
//		pulse();
}


function setScreenshot(path_thumbnail, path_full) {
    // Loader
//		$('#tv img').attr('src', path_thumbnail);
    $('#tv img').attr('src', path_thumbnail).attr('rel', path_full);
}


function filterGames() {
    classes = "";
    $.each(activeFilters, function (filter, active) {
        if (active) classes += '.' + filter;
    });

    $('.game-card:not(' + classes + ')').fadeOut('fast');
    $('.game-card' + classes).fadeIn('fast');
    $('#gameCount').html($('.game-card' + classes).length);
}


function sortGameCards(attribute, direction) {
    if (!attribute) return;

    if (attribute == 'random') {
        $('#game-cards .game-card').sort(function (a, b) {
            return Math.random() < 0.5 ? -1 : 1;
        }).appendTo($('#game-cards'));
    } else {
        var direction = direction == 'asc' ? 1 : -1;
        $('#game-cards .game-card').sort(function (a, b) {
            return ($(a).data(attribute) > $(b).data(attribute)) ? direction : ($(a).data(attribute) < $(b).data(attribute)) ? -direction : 0;
        }).appendTo($('#game-cards'));
    }
}