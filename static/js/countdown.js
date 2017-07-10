// JavaScript Document
(function($){
	//传入的$为jq对象
	$.fn.countdown = function(options){
		var defaults = {
            initcount:10,
			end:function(){},
			start:function(){},
			down:function(){}
        }
		//合并配置
		var opts = jQuery.extend({}, defaults, options);

		return this.each(function(index, element) {
			var obj = $(this);
			var count = opts.initcount;
			var go = function (count){
				if(count == 0){
					opts.end();

				}
				obj.html(count);
				setTimeout(function(){
					opts.down();
					count--;
					go(count);
				},1000);
			}
			go(opts.initcount);
		});
	}
})(jQuery);