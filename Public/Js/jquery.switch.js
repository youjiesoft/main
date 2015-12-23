// JavaScript Document
/*
 * switchCheck
 * selector: input[type=checkbox]
 */

!(function($) {
	$.fn.switchCheck = function(opts) {
		var options = $.extend({}, $.fn.switchCheck.defaults, opts);
		return this.each(function() {
			var $this = $(this);
			if($this.prev().hasClass('switch')) {
				return false;
			}

			var	value = $this.val(),
				$wrapper = $('<div class="switch"></div>'),
				$container = $('<div class="switch-container"></div>'),
				$on = $('<span class="switch-left"></span>'),
				$off = $('<span class="switch-right"></span>'),
				$label = $('<span class="switch-label"></span>');

			$this.removeClass('switch-check');

			if($this.prop('checked')) {
				$container.addClass('switch-on');
			} else {
				$container.addClass('switch-off');
			}
			

			// 原始输入控件值同步更新
			function updateValue() {
				$this.trigger('click');
			}

			$container.append($on);
			$container.append($label);
			$container.append($off);
			$wrapper.append($container).insertBefore($this);
			$this.hide();

			$wrapper.on('click', function(e) {
				if(!$this.prop('disabled')) {
					$container.toggleClass('switch-on switch-off');
					updateValue();
				} else {
					return false;
				}
			});
		});
	}
})(jQuery);

$.fn.switchCheck.defaults = {

};