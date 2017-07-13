/**
 * Created by wujunyuan on 2017/7/13.
 */
if (ret.start == true) {
    if (ret.gamestatus == 1) {
        //刚准备好，发牌完毕
        $('.cards').show();
        ('.tanpai').show();

        $('#gameready').hide();
        //摊牌倒计时
        tanpaicountdown(ret.room.taipaitime);
    } else if (ret.gamestatus == 2) {
        //已经摊牌
        $('.tanpai').hide();
        $('.cards .niuname').html('<img width="120" src="__STATIC__/game/img/niu' + ret.info + '.png" >').show();


    }
    for (var k = 0; k < ret.pai.length; k++) {
        $('.cards .pai img').eq(k).attr('src', '__STATIC__/game/img/pai/' + ret.pai[k] + '.png');
    }
}