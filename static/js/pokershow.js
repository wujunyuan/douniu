var poker = {
    fanpai: function (pai, imgpath, key) {
        var _this = $(pai);
        if (_this.hasClass('open') || key == 0) {
            return false;
        }
        _this.flip({
            direction: 'lr',
            content: '<img class="pk" src="' + imgpath + key + '.png">',
            speed: 100,
            onEnd: function () {

                _this.addClass('open');
            }
        });
    },
    fanpaiall: function (elements, imgpath, keys) {
        $(elements).each(function (index, element) {
            poker.fanpai(element, imgpath, keys[index]);
        });
    },
    init: function (poker) {
        $(poker).removeClass('open');
        $(poker).find('img').attr('src', 'static/game/img/pai/0.png');
    }
}