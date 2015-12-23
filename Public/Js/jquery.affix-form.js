/**
 * Affix form title
 * @author xyzhanjiang
 */

!(function($) {
	$.fn.affixFormTitle = function() {
		return this.each(function() {
            var $content = $(this),
				$title = $content.find('.form-affix-title'),
				$box = $content.closest(".pageFormContent");
            function fixedFormTitle() {
                var scrollH = $box.scrollTop();
                if(scrollH >= 15) {
                    if(!$title.hasClass('affix')) {
                        $title.addClass('affix');
                        $content.addClass('affix');
                    }
                } else {
                    $title.removeClass('affix');
                    $content.removeClass('affix');
                }
            }
            $box.on("scroll", function(){
                fixedFormTitle();
            });
		});
	};
})(jQuery);