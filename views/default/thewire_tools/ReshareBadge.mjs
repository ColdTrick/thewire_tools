import 'jquery';
import lightbox from 'elgg/lightbox';

$(document).on('click', '.elgg-menu-item-thewire-tools-reshare > a > .elgg-badge', function(event) {
	var data = $(this).closest('a').data();
	if (!data || !data.badgeLink) {
		return true;
	}
	
	event.preventDefault();
	
	lightbox.open({
		href: data.badgeLink,
		maxHeigth: '85%'
	});
	
	return false;
});
