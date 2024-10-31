$(function()
{
	$('div').live('pageshow', function(event, ui)
	{
		var width = 0;
		var widthOne = 0;
		var $slider = $('.nomis-photos', this);
		
		if (!$slider.hasClass('slider-loaded'))
		{
			$('li', $slider).each(function(i, el)
			{
				width += (parseInt($(el).width()) + parseInt($(el).css('margin-right')));

				if (widthOne == 0)
				{
					widthOne = width;
				}
			});
			
			$slider.width(width);
			$slider.data('width', width);
//			$slider.data('')

			var $wrapper = $('.nomis-photos-wrapper', this);
			var wrapperWidth = _WIDTH;//$wrapper.width();//$('.ui-content').width();
			$slider.data('wrapperwidth', wrapperWidth);
			
			if (width > wrapperWidth)
			{
				$slider.data('left', 0);

				function sliderToLeft()
				{
					var $slider = $('.nomis-photos', $(this).parent());
					var left = $slider.data('left');
					
					if (left < 0)
					{
						$slider.animate({
							left: '+=' + widthOne + 'px'
						});

						$slider.data('left', left + widthOne);
					}
				}

				function sliderToRight()
				{
					var $slider = $('.nomis-photos', $(this).parent());
					var left = $slider.data('left');
					
					console.log(left);
					console.log($slider.data('width'));
					console.log($slider.data('wrapperwidth'));
					
					if (-left < $slider.data('width') - $slider.data('wrapperwidth'))
					{
						$('.nomis-photos', $(this).parent()).animate({
							left: '-=' + widthOne + 'px'
						});

						$slider.data('left', left - widthOne);
					}
				}

				$('<div/>').attr('class', 'arrow prev').click(sliderToLeft).prependTo($wrapper);
				$('<div/>').attr('class', 'arrow next').click(sliderToRight).appendTo($wrapper);

				$wrapper.bind('swiperight', function()
				{
					$('.arrow.prev', this).click();
				});
				$wrapper.bind('swipeleft', function()
				{
					$('.arrow.next', this).click();
				});

				$slider.css({
					position: 'absolute',
					top: 0,
					left: 0
				});
			}
			
			$slider.addClass('slider-loaded');
		}

		$('img', this).lazyload();

//		$('#maps-holder p').html('').append($('<img/>').attr('src', 'http://maps.google.com/maps/api/staticmap?center=<?php echo $data['property']['lat']; ?>,<?php echo $data['property']['lng']; ?>&zoom=13&size=' + wrapperWidth + 'x200&markers=color:blue|label:A|<?php echo $data['property']['lat']; ?>,<?php echo $data['property']['lng']; ?>&sensor=false'));
	});
	
	$('#intro').trigger('pageshow');
	
	var geoPosition;
	var found = false;
//	$('#geo-checkbox').hide();
	$('#geo-button').hide();
	var $checkbox = $('#enable-geo');
	function geoSuccess(position)
	{
		if (found)
		{
			// not sure why we're hitting this twice in FF, I think it's to do with a cached result coming back    
			return;
		}
		
		found = true;
		
		geoPosition = position.coords;
//		$('#geo-checkbox input[name="geo"]').val(position.coords.latitude + ',' + position.coords.longitude);
		$('#geo-button').attr('href', 'index.php?action=search&geo='
			+ position.coords.latitude + ',' + position.coords.longitude
			+ '&geo-enabled=1');
//		$checkbox.change();
//		$('#geo-checkbox').show();
		
	}
	
//	console.log($checkbox);
	
	$checkbox.change(function()
	{
		if (found)
		{
			if (this.checked)
			{
				$('#geo-city').hide();
			}
			else
			{
				$('#geo-city').show();
			}
		}
	});

	if (navigator.geolocation)
	{
		navigator.geolocation.getCurrentPosition(geoSuccess, function()
		{
			$('#geo-checkbox').hide();
		});
	}
	else
	{
		geoError('not supported');
	}
});