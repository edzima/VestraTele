for (var i = 0, element; !!(element = document.getElementsByClassName('toggle')[i]); i++) {
	element.onclick = function (evt) {
		toggle(evt.currentTarget.getAttribute('data-toggle'));
	};

	if (element.classList.contains('default-hide')) {
		toggle(element.getAttribute('data-toggle'));
	}

	function toggle(selector) {
		$(selector).toggle();
	}
}