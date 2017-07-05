<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:78:"/home/wwwroot/douniu.sppcms.com/douniu/application/index/view/index/index.html";i:1498892314;s:77:"/home/wwwroot/douniu.sppcms.com/douniu/application/index/view/public/top.html";i:1498892314;s:80:"/home/wwwroot/douniu.sppcms.com/douniu/application/index/view/public/bottom.html";i:1498892314;}*/ ?>
<!DOCTYPE html>
<html>
<head>
<title>Home</title>
<!-- for-mobile-apps -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
		function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- //for-mobile-apps -->
<link href="__STATIC__/index/css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
<!-- pignose css -->
<link href="__STATIC__/index/css/pignose.layerslider.css" rel="stylesheet" type="text/css" media="all" />
<link href="__STATIC__/index/css/flexslider.css" rel="stylesheet" type="text/css" media="all" />

<!-- //pignose css -->
<link href="__STATIC__/index/css/style.css" rel="stylesheet" type="text/css" media="all" />
<!-- js -->
<script type="text/javascript" src="__STATIC__/index/js/jquery-2.1.4.min.js"></script>
<!-- //js -->
<!-- cart -->
	
		
<!-- cart -->
<!-- for bootstrap working -->
	<script type="text/javascript" src="__STATIC__/index/js/bootstrap-3.1.1.min.js"></script>
<!-- //for bootstrap working -->
<link href='http://fonts.useso.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/html'>
<link href='http://fonts.useso.com/css?family=Lato:400,100,100italic,300,300italic,400italic,700,900,900italic,700italic' rel='stylesheet' type='text/html'>
<script src="__STATIC__/index/js/jquery.easing.min.js"></script>
</head>
<body>

<!-- header-bot -->
<div class="header-bot">
	<div class="container">
		<div class="col-md-3 header-left">
			<h1><a href="index.html"><img src="<?php echo getconfig('logo'); ?>"></a></h1>
		</div>
		<div class="col-md-6 header-middle">
			<form method="get" action="<?php echo url('category/index'); ?>">
				<div class="search">
					<input name="keywords" type="search" placeholder="请输入关键词 " value="">
				</div>
				<div class="section_room">
					<select name="id" id="country" onChange="change_country(this.value)" class="frm-field required">
						<option value="0">所有分类</option>
                        <?php if(is_array($categorylist) || $categorylist instanceof \think\Collection || $categorylist instanceof \think\Paginator): $i = 0; $__LIST__ = $categorylist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
						<option value="<?php echo $vo['id']; ?>"><?php echo $vo['title']; ?></option>     
						<?php endforeach; endif; else: echo "" ;endif; ?>
						
					</select>
				</div>
				<div class="sear-sub">
					<input type="submit" value=" ">
				</div>
				<div class="clearfix"></div>
			</form>
		</div>
		<div class="col-md-3 header-right footer-bottom">
			<ul>
				
				<?php if($islogin): ?>
					欢迎您：<a href="<?php echo url('Member/index/index'); ?>"><?php echo $memberinfo['nickname']; ?></a>
					
					<a href="<?php echo url('member/Login/regout'); ?>">退出</a>
                    <?php else: ?>
                    <li style="float:left; margin-left:50px;"><a href="#" class="use1" data-toggle="modal" data-target="#myModal4"><span>登录</span></a>
					
				</li>
				<?php endif; ?>
			</ul>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<!-- //header-bot -->
<!-- banner -->
<div class="ban-top">
	<div class="container">
		<div class="top_nav_left">
			<nav class="navbar navbar-default">
			  <div class="container-fluid">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
				  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				  </button>
				</div>
				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse menu--shylock" id="bs-example-navbar-collapse-1">
				  <ul class="nav navbar-nav menu__list">
                  
                  
                  
                  <?php if(is_array($navlist) || $navlist instanceof \think\Collection || $navlist instanceof \think\Paginator): $i = 0; $__LIST__ = $navlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                  
					
					
					<li class="dropdown menu__item">
						<a target="<?php echo $vo['target']; ?>" href="<?php echo $vo['link']; ?>" class="<?php if(!(empty($vo['sub']) || (($vo['sub'] instanceof \think\Collection || $vo['sub'] instanceof \think\Paginator ) && $vo['sub']->isEmpty()))): ?>dropdown-toggle <?php endif; ?>menu__link" <?php if(!(empty($vo['sub']) || (($vo['sub'] instanceof \think\Collection || $vo['sub'] instanceof \think\Paginator ) && $vo['sub']->isEmpty()))): ?>data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"<?php endif; ?>><?php echo $vo['name']; if(!(empty($vo['sub']) || (($vo['sub'] instanceof \think\Collection || $vo['sub'] instanceof \think\Paginator ) && $vo['sub']->isEmpty()))): ?><span class="caret"></span><?php endif; ?></a>
                        
                        
                        <?php if(!(empty($vo['sub']) || (($vo['sub'] instanceof \think\Collection || $vo['sub'] instanceof \think\Paginator ) && $vo['sub']->isEmpty()))): if(is_array($vo['sub']) || $vo['sub'] instanceof \think\Collection || $vo['sub'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$svo): $mod = ($i % 2 );++$i;?>
							<ul class="dropdown-menu multi-column columns-3">
								<div class="row">
									<div class="col-sm-3 multi-gd-img">
										<ul class="multi-column-dropdown">
											<li><a target="<?php echo $svo['target']; ?>" href="<?php echo $vo['link']; ?>"><?php echo $svo['name']; ?></a></li>
										</ul>
									</div>
									
									
									<div class="clearfix"></div>
								</div>
							</ul>
                            <?php endforeach; endif; else: echo "" ;endif; endif; ?>
					</li>
                    
                    
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                    
					
				  </ul>
				</div>
			  </div>
			</nav>	
		</div>
		<div class="top_nav_right">
			<div class="cart box_1">
						<a href="<?php echo url('Carts/index'); ?>">
							<h3> <div class="total">
								<i class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></i>
								<span class="simpleCart_total"></span> (<span id="simpleCart_quantity" class="simpleCart_quantity">0</span>个商品)</div>
								
							</h3>
						</a>
						<p><a href="javascript:;" class="simpleCart_empty">购物车</a></p>
			</div>	
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<!-- //banner-top -->
<!-- banner -->


<?php if(!(empty($adlist) || (($adlist instanceof \think\Collection || $adlist instanceof \think\Paginator ) && $adlist->isEmpty()))): ?>
<div class="banner-grid">
	<div id="visual">
			<div class="slide-visual">
				<!-- Slide Image Area (1000 x 424) -->
				<ul class="slide-group">
                
                <?php if(is_array($adlist) || $adlist instanceof \think\Collection || $adlist instanceof \think\Paginator): $i = 0; $__LIST__ = $adlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                
					<li><a href="<?php echo $vo['link']; ?>"><img class="img-responsive" src="<?php echo $vo['img_url']; ?>" alt="<?php echo $vo['title']; ?>" /></a></li>

					<?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>

				<!-- Slide Description Image Area (316 x 328) -->
				<div class="script-wrap">
					
					<div class="slide-controller">
						<a href="#" class="btn-prev"><img src="__STATIC__/index/images/btn_prev.png" alt="Prev Slide" /></a>
						
						<a href="#" class="btn-next"><img src="__STATIC__/index/images/btn_next.png" alt="Next Slide" /></a>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="clearfix"></div>
		</div>
	<script type="text/javascript" src="__STATIC__/index/js/pignose.layerslider.js"></script>
	<script type="text/javascript">
	//<![CDATA[
		$(window).load(function() {
			$('#visual').pignoseLayerSlider({
				play    : '.btn-play',
				pause   : '.btn-pause',
				next    : '.btn-next',
				prev    : '.btn-prev'
			});
		});
	//]]>
	</script>

</div> 
<?php endif; ?>


<div class="product-easy">
	<div class="container">
		
		<script src="__STATIC__/index/js/easyResponsiveTabs.js" type="text/javascript"></script>
		<script type="text/javascript">
							$(document).ready(function () {
								$('#horizontalTab').easyResponsiveTabs({
									type: 'default', //Types: default, vertical, accordion           
									width: 'auto', //auto or any width like 600px
									fit: true   // 100% fit in a container
								});
							});
							
		</script>
		<div class="sap_tabs">
			<div id="horizontalTab" style="display: block; width: 100%; margin: 0px;">
				<ul class="resp-tabs-list">
                
                
                <?php if(is_array($catelist) || $catelist instanceof \think\Collection || $catelist instanceof \think\Paginator): $i = 0; $__LIST__ = $catelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
					<li class="resp-tab-item" aria-controls="tab_item-<?php echo $key; ?>" role="tab"><span><?php echo $vo['title']; ?></span></li> 
					<?php endforeach; endif; else: echo "" ;endif; ?>
                    
				</ul>				  	 
				<div class="resp-tabs-container"><?php if(is_array($catelist) || $catelist instanceof \think\Collection || $catelist instanceof \think\Paginator): $i = 0; $__LIST__ = $catelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
					<div class="tab-1 resp-tab-content" aria-labelledby="tab_item-<?php echo $key; ?>">
                    
                    
                    
                    
                         
                    
                    <?php if(is_array($vo['product']) || $vo['product'] instanceof \think\Collection || $vo['product'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['product'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pvo): $mod = ($i % 2 );++$i;?>
                    
                    
						<div class="col-md-3 product-men">
      <div class="men-pro-item simpleCart_shelfItem">
        <div class="men-thumb-item"> <img src="<?php echo $pvo['img_url']; ?>" alt="" class="pro-image-front"> <img src="<?php echo $pvo['img_url']; ?>" alt="" class="pro-image-back">
          <div class="men-cart-pro">
            <div class="inner-men-cart-pro"> <a href="<?php echo url('product/index', array('id' => $pvo['id'])); ?>" class="link-product-add-cart">查看详情</a> </div>
          </div>
        </div>
        <div class="item-info-product ">
          <h4><div style="padding:10px; text-align:left; line-height:1.5;"><a href="<?php echo url('product/index', array('id' => $pvo['id'])); ?>"><?php echo $pvo['title']; ?></a></div></h4>
          <div class="info-product-price"> <span class="item_price">¥<?php echo $pvo['price']; ?></span> <del>¥<?php echo $pvo['originalCost']; ?></del> </div>
          
          
          <form class="addcartform" callback="s" id="addtocart<?php echo $pvo['id']; ?>" action="<?php echo url('Carts/add'); ?>">
							<input type="hidden" name="product_num" id="num" value="1"/>
							<input type="hidden" name="product_id" value="<?php echo $pvo['id']; ?>">
                            </form>
          
          <a href="##" onClick="$('#addtocart<?php echo $pvo['id']; ?>').submit();" class="item_add single-item hvr-outline-out button2">加入购物车</a> </div>
      </div>
    </div>
                        
                          <?php endforeach; endif; else: echo "" ;endif; ?>
                        
                        
                        
                        
                        
                        
                        
                        
						
						
						
						
						
						
						
						
						
						
						
						<div class="clearfix"></div>
					</div>
					<?php endforeach; endif; else: echo "" ;endif; ?>
						
				</div>	
			</div>
		</div>
	</div>
</div>
<!-- //product-nav -->

<!-- footer -->
<div class="footer">
	<div class="container">
		<div class="col-md-4 footer-left">
			<h2><a href="index.html"><img src="<?php echo getconfig('logo'); ?>" alt=" " /></a></h2>
			<p><?php echo getconfig('siteinfo'); ?>漂亮裙子优惠专场，实时更新高性价比漂亮裙子单品、漂亮裙子打折特卖信息，全场低至1折起包邮，敬请关注！这些漂亮裙子都是由专业编辑为您精挑细选的，时下最好、最流行的，帮您节省浏览海量商品信息时间，让您以更优惠的折扣价格购买到自己喜欢的漂亮裙子！</p>
		</div>
		<div class="col-md-8 footer-right">
			
			<div class="clearfix"></div>
			<div class="sign-grds">
				
				<div class="col-md-7 sign-gd-two">
					<h4>店铺信息</h4>
					<ul>
						<li><i class="glyphicon glyphicon-map-marker" aria-hidden="true"></i>地址: <?php echo getconfig('address'); ?></li>
						<li><i class="glyphicon glyphicon-envelope" aria-hidden="true"></i>邮箱 : <a href="mailto:<?php echo getconfig('email'); ?>"><?php echo getconfig('email'); ?></a></li>
						<li><i class="glyphicon glyphicon-earphone" aria-hidden="true"></i>电话:<?php echo getconfig('tel'); ?></li>
					</ul>
				</div>
				<div class="col-md-5 sign-gd flickr-post">
					<h4>扫描二维码有惊喜哦！</h4>
					<img src="__STATIC__/images/21925ad81c3e406deff8070f3be7e30f.png" width="200" height="200" alt=""/>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="clearfix"></div>
		
	</div>
</div>
<!-- //footer -->

<!-- login -->
<div class="modal fade" id="myModal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
<div class="modal-dialog" role="document">
<div class="modal-content modal-info">
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>						
</div>
<div class="modal-body modal-spa">
	<div class="login-grids">
		<div class="login">
			<div class="login-bottom">
				<h3>注册
				<span id="errormsg" style="font-size:12px; color:#f00;">
					
				</span><span id="successmsg" style="font-size:12px; color:green;">
					
				</span>
				</h3>
				<form action="<?php echo url('Member/Login/doregister'); ?>" id="register"  callback="none">
					<div class="sign-up">
						<h4>邮箱</h4>
						<input type="text" value="" placeholder="请输入账号" id="email" name="email">	
					</div>
					<div class="sign-up">
						<h4>昵称</h4>
						<input type="text" placeholder="请输入昵称" name="nickname">
						
					</div>
					<div class="sign-up">
						<h4>密码</h4>
						<input type="password" id="password" name="password">
						
					</div>
					<div class="sign-up">
						<h4>确认密码</h4>
						<input type="password" name="repassword">
						
					</div>
					<div class="sign-up">
						<input type="submit" value="立即注册">
					</div>
					
				</form>
			</div>
			<div class="login-right">
				<h3>登录<span id="errormsg" style="font-size:12px; color:#f00;">
					
				</span><span id="successmsg" style="font-size:12px; color:green;">
					
				</span></h3>
				<form action="<?php echo url('Member/Login/doLogin'); ?>" method="post" callback="none">
					<div class="sign-in">
						<h4>邮箱</h4>
						<input type="text" name="Email" value="<?php echo (\think\Cookie::get('Email')) ? \think\Cookie::get('Email') :  ''; ?>">	
					</div>
					<div class="sign-in">
						<h4>密码</h4>
						<input type="password" value="<?php echo (\think\Cookie::get('password')) ? \think\Cookie::get('password') :  ''; ?>" name="password">
						<a href="#">忘记密码</a>
					</div>
					<div class="single-bottom">
						
						<input type="checkbox"  id="brand" name="remember" value="on" <?php if(isset($_COOKIE['remember'])): ?>checked<?php endif; ?>>
						<label for="brand"><span></span>记住密码</label>
					</div>
					<div class="sign-in">
						<input type="submit" value="立即登录" >
					</div>
				</form>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
</div>
</div>
</div>
<!-- //login -->
<script>
	$('#register').submit(function(e) 
	{
    var data = $(this).serialize();
	$.ajax({
		url:$(this).attr('action'),
		type:'POST',
		dataType:"json",
		data:data,
		success: function(ret){
			 if(ret.code == 0){
			 	$('#errormsg').html(ret.msg);
			 }else{
			 		$('#successmsg').html(ret.msg);
			 		setTimeout(function(){
			 			window.location.reload();
			 		},500);
			 		
				}
		}
	});
	return false;
});

	$('#login').submit(function(e){
		var data = $(this).serialize();
		//return false;
		$.ajax({
			url:$(this).attr('action'),
			type:'POST',
			dataType:"json",
			data:data,
			success: function(ret){
				 if(ret.code == 0){
					$('#errormsg').html(ret.msg);
				 }else{
						$('#successmsg').html(ret.msg);
						setTimeout(function(){
							window.location.reload();
						},500);
						
					}
			}
		});
		return false;
	});
	
//购物车数量
$.get("<?php echo url('Carts/getCart'); ?>", {}, function(data){
	$('#simpleCart_quantity').html(data.num);
}, 'json');



</script>
<script src="__STATIC__/js/setajax.js"></script>
<script src="__STATIC__/index/js/jquery.flexslider.js"></script>
<script src="__STATIC__/index/js/imagezoom.js"></script><script>
    $('.addcartform').ajaxsubmit({error:function(ret){
		if(ret.code == 0){
			$('#myModal4').modal('show');
		}
}});
$('form[callback="jump"]').ajaxsubmit();
</script>
</body>
</html>

