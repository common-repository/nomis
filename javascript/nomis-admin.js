$(document).ready(function()
{
	$('.uncheck-all').change(function()
	{
		if (this.checked)
        {
			$('input', $(this).parent().parent()).attr('checked', 'checked');
        }
        else
        {
            $('input', $(this).parent().parent()).attr('checked', '');
        }
	});

	$('#nomis-property-sale-details').sortable(
	{
		handle: '.handle',
		update: function()
		{
			var order = $(this).find('ul');
			var orders = '';
			order.each(function()
			{
				orders += $(this).sortable('serialize').split(/\[\]=[0-9]+&*/g).join(',');
			});

			$('#property-sale-details-order').val(orders);
		}
	});
	$('#nomis-property-bog-details').sortable(
	{
		handle: '.handle',
		update: function()
		{
			var order = $(this).find('ul');
			var orders = '';
			order.each(function()
			{
				orders += $(this).sortable('serialize').split(/\[\]=[0-9]+&*/g).join(',');
			});

			$('#property-bog-details-order').val(orders);
		}
	});
	$('.details-category ul').sortable(
	{
		handle: '.handle',
		update: function()
		{
			var order = $(this).parent().parent().find('ul');
			var orders = '';
			order.each(function()
			{
				orders += $(this).sortable('serialize').split(/\[\]=[0-9]+&*/g).join(',');
			});

			console.log(orders);

			$('#property-sale-details-order').val(orders);
		}
	});

	$("#nomis-property-details").easyListSplitter(
	{
		colNumber: 3
	});
	$('.property-details').sortable(
	{
		handle: '.handle',
		connectWith: '.property-details',
		update: function()
		{
			var order = $('#nomis-property-details').parent().find('ul');
			var orders = '';
			order.each(function(){
				orders += $(this).sortable('serialize').split(/\[\]=[0-9]+&*/g).join(',');
			});

			$('#property-details-order').val(orders);
		}
	});

	$("#nomis-properties-details").easyListSplitter(
	{
		colNumber: 3
	});
	$('.properties-details').sortable(
	{
		handle: '.handle',
		connectWith: '.properties-details',
		update: function()
		{
			var order = $('#nomis-properties-details').parent().find('ul');
			var orders = '';
			order.each(function(){
				orders += $(this).sortable('serialize').split(/\[\]=[0-9]+&*/g).join(',');
			});

			$('#properties-details-order').val(orders);
		}
	});

	$("#nomis-random-properties-details").easyListSplitter(
	{
		colNumber: 3
	});
	$('.nomis-random-properties-details').sortable(
	{
		handle: '.handle',
		connectWith: '.nomis-random-properties-details',
		update: function()
		{
			var order = $('#nomis-random-properties-details').parent().find('ul');
			var orders = '';
			order.each(function(){
				orders += $(this).sortable('serialize').split(/\[\]=[0-9]+&*/g).join(',');
			});

			$('#nomis-properties-random-details-order').val(orders);
		}
	});

	$('#quick-search-criteria').sortable(
	{
		handle: '.handle',
		update: function()
		{
			$('#nomis-quick-search-criteria').val($(this).sortable('serialize').substr(30).split(/\[\]=[0-9]+&*/g).join(','));
		}
	});

	$('#search-criteria').sortable(
	{
		handle: '.handle',
		update: function()
		{
			$('#nomis-search-criteria').val($(this).sortable('serialize').substr(24).split(/\[\]=[0-9]+&*/g).join(','));
		}
	});

	$('#search-criteria-sale').sortable(
	{
		handle: '.handle',
		update: function()
		{
			$('#nomis-search-criteria-sale').val($(this).sortable('serialize').substr(29).split(/\[\]=[0-9]+&*/g).join(','));
		}
	});
});