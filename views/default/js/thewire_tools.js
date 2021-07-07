define(['jquery', 'elgg/Ajax'], function($, Ajax) {
	var ajax = new Ajax();
	
	$(document).on('click', '.elgg-menu-item-thread a', function (event) {
		var guid = $(this).attr('rel');
		var $placeholder = $('#thewire-thread-' + guid);

		if (!$placeholder.length) {
			return;
		}

		if ($placeholder.is(':visible')) {
			$placeholder.hide();
			return false;
		}

		if ($placeholder.html().length) {
			$placeholder.show();
			return false;
		}
		
		ajax.view('thewire_tools/thread', {
			data: $placeholder.data(),
			success: function(result) {
				$placeholder.html(result).show();
			}
		});
		
		return false;
	});
});
