import 'jquery';
import Ajax from 'elgg/Ajax';

$(document).on('click', '.elgg-menu-item-reply a[data-thewire-reply]:not(.elgg-toggle)', function(event) {
	event.preventDefault();
	
	var guid = $(this).data('thewireReply');
	var $menu_item = $(this);
	
	var ajax = new Ajax();
	ajax.view('thewire_tools/reply_loader', {
		data: {
			guid: guid
		},
		success: function(data) {
			$('#thewire-tools-reply-' + guid).replaceWith(data);
			$menu_item.addClass('elgg-toggle');
		}
	});
	
	return false;
});
