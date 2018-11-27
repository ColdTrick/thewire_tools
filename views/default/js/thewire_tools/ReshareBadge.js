define(function(require){
	
	var $ = require('jquery');
	var lightbox = require('elgg/lightbox');
	
	var badge_click = function(event) {
		
		var $link = $(this).closest('a');
		var data = $link.data();
		
		if (!data || !data.badgeLink) {
			return true;
		}
		
		event.preventDefault();
		
		lightbox.open({
			href: data.badgeLink,
			maxHeigth: '85%'
		});
		
		return false;
	};
	
	$(document).on('click', '.elgg-menu-item-thewire-tools-reshare > a > .elgg-badge', badge_click);
});
