(function($, undefined)
{
	$('html').removeClass('no-js');
	var latLng;
	var mapsTitle;
	

	$(function()
	{
		if ($('#nomis-maps').data('lat') != '' && $('#nomis-maps').data('lat') != undefined)
		{
			latLng = new google.maps.LatLng($('#nomis-maps').data('lat'), $('#nomis-maps').data('lng'));


			$('#show-maps').click(function()
			{
				$('#media-holder > div, #media-holder > table').hide();
				$('#show-maps').addClass('active');
				$('#show-streetview').removeClass('active');
				showMaps();
			});			

			$('#show-streetview').click(function()
			{
				$('#media-holder > div, #media-holder > table').hide();
				$('#show-streetview').addClass('active');
				$('#show-maps').removeClass('active');
				showStreetView();
			});

			$('.location-first').click();
		}

		(function()
		{
			if ($('#grand-map')[0])
			{
				var myOptions = {
					zoom: 11,
					//center: new google.maps.LatLng(-34.397, 150.644),
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
				$('#map-addresses').hide();
				$('#grand-map').show();

				var map = new google.maps.Map($('#grand-map')[0], myOptions);


				var latLng, north = 0, south = 100000, west = 0, east = 10000;

				$('#map-addresses li').each(function()
				{
					if ($(this).data('lat') > north) north = $(this).data('lat'); // y
					if ($(this).data('lat') < south) south = $(this).data('lat');

					if ($(this).data('lng') > west) west = $(this).data('lng'); // x
					if ($(this).data('lng') < east) east = $(this).data('lng');

					latLng = new google.maps.LatLng($(this).data('lat'), $(this).data('lng'));
					var marker = new google.maps.Marker({
						position: latLng,
						map: map,
						title: $(this).text()
					});

					var infowindow = new google.maps.InfoWindow({
						content: '<p style="font: 12px/14px Arial"><a href="'+$(this).data('url')+'">' + $(this).text() + '</a><br>'
							+'Interior: '+$(this).data('interior')+', bedrooms: '+$(this).data('bedrooms')+'<br> Price: &euro;'+$(this).data('price')+'</p>'
					});

					google.maps.event.addListener(marker, 'click', function()
					{
						infowindow.open(map, marker);
					});
				});

				map.setCenter(new google.maps.LatLng(
					(north + south) / 2,
					(west + east) / 2
				));
			}
		})();
	});


	function showMaps()
	{
		$('#media-holder #nomis-maps').show();

		if (!$('#media-holder #nomis-maps').hasClass('activated'))
		{
			var myOptions = {
				zoom: 13,
				center: latLng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}

			var map = new google.maps.Map($('#media-holder #nomis-maps')[0], myOptions);

			var marker = new google.maps.Marker({
				position: latLng,
				map: map,
				title: $('#nomis-maps').data('title')
			});
		}

		$('#media-holder #nomis-maps').addClass('activated');
	}

	function showStreetView()
	{
		$('#media-holder #nomis-streetview').show();

		if (!$('#media-holder #nomis-streetview').hasClass('activated'))
		{
			var panoramaOptions = {
				position: latLng,
				pov: {
					heading: 0,
					pitch: 0,
					zoom: 0
				}
			};

			var panorama = new  google.maps.StreetViewPanorama($('#media-holder #nomis-streetview')[0], panoramaOptions);
		}

		$('#media-holder #nomis-streetview').addClass('activated');
	}
})(jQuery);