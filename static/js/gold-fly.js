// JavaScript Document
(function($){
	//传入的$为jq对象
	$.fn.goldfly = function(options){
		/**
		 *
		 * @type {{data: {}, selector: string, element: string}}
         */
		var defaults = {
            data: {},
			selector:'',
			element:''
        }
		//合并配置
		var opts = jQuery.extend({}, defaults, options);
		return this.each(function(index, element) {
			var _this = $(this);
			_this.clone().appendTo().animate({},'','',function(){

			})
		});
	}
})(jQuery);