<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:64:"F:\www\pyg\public/../application/usercenter\view\info\index.html";i:1570721511;s:57:"F:\www\pyg\application\usercenter\view\Common\layout.html";i:1570608822;s:60:"F:\www\pyg\application\usercenter\view\Common\left_menu.html";i:1570722445;}*/ ?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />

	<link rel="stylesheet" type="text/css" href="/static/home/css/all.css" />

	<script type="text/javascript" src="/static/home/js/all.js"></script>
	<script src="/static/home/js/plugins/layer/layer.js"></script>
</head>

<body>
	<!-- 头部栏位 -->
	<!--页面顶部-->
	<div id="nav-bottom">
		<!--顶部-->
		<div class="nav-top">
			<div class="top">
				<div class="py-container">
					<div class="shortcut">
						<ul class="fl">
							<?php if(!(empty(\think\Session::get('home_userinfo')) || ((\think\Session::get('home_userinfo') instanceof \think\Collection || \think\Session::get('home_userinfo') instanceof \think\Paginator ) && \think\Session::get('home_userinfo')->isEmpty()))): ?>
							<li class="f-item"><font color='red'><?php echo (\think\Session::get('home_userinfo.nickname') ?: \think\Session::get('home_userinfo.username')); ?></font>  品优购欢迎您！</li>
							<li class="f-item"><span><a href="<?php echo url('home/loginRegister/logout'); ?>">注销</a></span></li>
							<?php else: ?>
							<li class="f-item">品优购欢迎您！</li>
							<li class="f-item">请<a href="<?php echo url('home/loginRegister/login'); ?>">登录</a>　<span><a href="<?php echo url('home/loginRegister/register'); ?>" target="_blank">免费注册</a></span></li>
							<?php endif; ?>
						</ul>
						<ul class="fr">
							<li class="f-item">我的订单</li>
							<li class="f-item space"></li>
							<li class="f-item"><a href="home.html" target="_blank">我的品优购</a></li>
							<li class="f-item space"></li>
							<li class="f-item">品优购会员</li>
							<li class="f-item space"></li>
							<li class="f-item">企业采购</li>
							<li class="f-item space"></li>
							<li class="f-item">关注品优购</li>
							<li class="f-item space"></li>
							<li class="f-item" id="service">
								<span>客户服务</span>
								<ul class="service">
									<li><a href="cooperation.html" target="_blank">合作招商</a></li>
									<li><a href="shoplogin.html" target="_blank">商家后台</a></li>
								</ul>
							</li>
							<li class="f-item space"></li>
							<li class="f-item">网站导航</li>
						</ul>
					</div>
				</div>
			</div>

			<!--头部-->
			<div class="header">
				<div class="py-container">
					<div class="yui3-g Logo">
						<div class="yui3-u Left logoArea">
							<a class="logo-bd" title="品优购" href="http://www.pyg.com" target="_blank"></a>
						</div>
						<div class="yui3-u Center searchArea">
							<div class="search">
								<form action="<?php echo url('home/goods/index'); ?>" method='get' class="sui-form form-inline">
									<!--searchAutoComplete-->
									<div class="input-append">
										<input type="text" id="" type="text" class="input-error input-xlarge" name="keywords" value="<?php echo \think\Request::instance()->param('keywords'); ?>"/>
										<button class="sui-btn btn-xlarge btn-danger" type="submit">搜索</button>
									</div>
								</form>
							</div>
							<div class="hotwords">
								<ul>
									<li class="f-item">品优购首发</li>
									<li class="f-item">亿元优惠</li>
									<li class="f-item">9.9元团购</li>
									<li class="f-item">每满99减30</li>
									<li class="f-item">亿元优惠</li>
									<li class="f-item">9.9元团购</li>
									<li class="f-item">办公用品</li>

								</ul>
							</div>
						</div>
						<div class="yui3-u Right shopArea">
							<div class="fr shopcar">
								<div class="show-shopcar" id="shopcar">
									<span class="car"></span>
									<a class="sui-btn btn-default btn-xlarge" href="cart.html" target="_blank">
										<span>我的购物车</span>
										<i class="shopnum">0</i>
									</a>
									<div class="clearfix shopcarlist" id="shopcarlist" style="display:none">
										<p>"啊哦，你的购物车还没有商品哦！"</p>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="yui3-g NavList">
						<div class="all-sorts-list">
							<div class="yui3-u Left all-sort">
								<h4>全部商品分类</h4>
							</div>
							<div class="sort">
								<div class="all-sort-list2">
									<?php if(is_array($category) || $category instanceof \think\Collection || $category instanceof \think\Paginator): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "暂无数据" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
									<div class="item bo">
										<h3><a href=""><?php echo $v['cate_name']; ?></a></h3>
										<div class="item-list clearfix">
											<div class="subitem">
												<?php if(is_array($v['son']) || $v['son'] instanceof \think\Collection || $v['son'] instanceof \think\Paginator): $i = 0; $__LIST__ = $v['son'];if( count($__LIST__)==0 ) : echo "暂无数据" ;else: foreach($__LIST__ as $key=>$v1): $mod = ($i % 2 );++$i;?>
												<dl class="fore1">
													<dt><a href=""><?php echo $v1['cate_name']; ?></a></dt>
													<dd>
														<?php if(is_array($v1['son']) || $v1['son'] instanceof \think\Collection || $v1['son'] instanceof \think\Paginator): $i = 0; $__LIST__ = $v1['son'];if( count($__LIST__)==0 ) : echo "暂无数据" ;else: foreach($__LIST__ as $key=>$v2): $mod = ($i % 2 );++$i;?>
														<em><a href="/goods/?cateid=<?php echo $v2['id']; ?>"><?php echo $v2['cate_name']; ?></a></em>
														<?php endforeach; endif; else: echo "暂无数据" ;endif; ?>
													</dd>
												</dl>
												<?php endforeach; endif; else: echo "暂无数据" ;endif; ?>
											</div>
										</div>
									</div>
									<?php endforeach; endif; else: echo "暂无数据" ;endif; ?>
								</div>
							</div>
						</div>
						<div class="yui3-u Center navArea">
							<ul class="nav">
								<li class="f-item">服装城</li>
								<li class="f-item">美妆馆</li>
								<li class="f-item">品优超市</li>
								<li class="f-item">全球购</li>
								<li class="f-item">闪购</li>
								<li class="f-item">团购</li>
								<li class="f-item">有趣</li>
								<li class="f-item"><a href="seckill-index.html" target="_blank">秒杀</a></li>
							</ul>
						</div>
						<div class="yui3-u Right"></div>
					</div>

				</div>
			</div>
		</div>
	</div>
	<title>设置-个人信息</title>
<link rel="icon" href="/assets/img/favicon.ico">
<link rel="stylesheet" type="text/css" href="/static/home/css/pages-seckillOrder.css" />


<!--header-->
<div id="account">
	<div class="py-container">
		<div class="yui3-g home">
			<!--左侧列表-->
			<div class="yui3-u-1-6 list">

    <div class="person-info">
        <div class="person-photo"><img src="<?php echo (\think\Session::get('home_userinfo.figure_url') ?: '/static/home/img/_/photo.png'); ?>" alt="" width="50" height="50"></div>
        <div class="person-account">
            <span class="name"><?php echo \think\Session::get('home_userinfo.nickname'); ?></span>
            <span class="safe">账户安全</span>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="list-items">
        <dl>
            <dt><i>·</i> 订单中心</dt>
            <dd><a href="seckillOrder.html">我的订单</a></dd>
            <dd><a href="seckillorder-pay.html">待付款</a></dd>
            <dd><a href="seckillorder-send.html">待发货</a></dd>
            <dd><a href="seckillorder-receive.html">待收货</a></dd>
            <dd><a href="seckillorder-evaluate.html">待评价</a></dd>
        </dl>
        <dl>
            <dt><i>·</i> 我的中心</dt>
            <dd><a href="seckillperson-collect.html">我的收藏</a></dd>
            <dd><a href="seckillperson-footmark.html">我的足迹</a></dd>
        </dl>
        <dl>
            <dt><i>·</i> 物流消息</dt>
        </dl>
        <dl>
            <dt><i>·</i> 关注中心</dt>
            <dd><a href="javascript:;">关注的商品 </a></dd>
            <dd><a href="javascript:;">关注的店铺 </a></dd>
            <dd><a href="javascript:;">关注的专辑 </a></dd>
            <dd><a href="javascript:;">关注的品牌 </a></dd>
            <dd><a href="javascript:;">关注的活动 </a></dd>
        </dl>
        <dl>
            <dt><i>·</i> 直播中心</dt>
            <dd><a href="<?php echo url('usercenter/live/index'); ?>">我的直播</a></dd>
        </dl>
        <dl>
            <dt><i>·</i> 我的中心</dt>
            <dd><a href="javascript:;">我的收藏</a></dd>
            <dd><a href="javascript:;">我的足迹</a></dd>
        </dl>
        <dl>
            <dt><i>·</i> 特色业务</dt>
            <dd><a href="javascript:;">我的营业厅 </a></dd>
            <dd><a href="javascript:;">通信 </a></dd>
            <dd><a href="javascript:;">定期送 </a></dd>
            <dd><a href="javascript:;">代下单</a></dd>
            <dd><a href="javascript:;">我的回收单</a></dd>
            <dd><a href="javascript:;">节能补贴</a></dd>
            <dd><a href="javascript:;">医药服务 </a></dd>
            <dd><a href="javascript:;">流量加油站</a></dd>
            <dd><a href="javascript:;">黄金托管</a></dd>
        </dl>
        <dl>
            <dt><i>·</i> 客户服务</dt>
            <dd><a href="javascript:;">返修退换货 </a></dd>
            <dd><a href="javascript:;">价格保护 </a></dd>
            <dd><a href="javascript:;">意见建议 </a></dd>
            <dd><a href="javascript:;">购买咨询 </a></dd>
            <dd><a href="javascript:;">交易纠纷 </a></dd>
            <dd><a href="javascript:;">我的发票 </a></dd>
        </dl>
        <dl>
            <dt><i>·</i> 设置</dt>
            <dd><a href="seckillsetting-info.html" class="list-active">个人信息</a></dd>
            <dd><a href="seckillsetting-address.html">地址管理</a></dd>
            <dd><a href="<?php echo url('usercenter/safe/index'); ?>">安全管理</a></dd>
        </dl>
    </div>
</div>

			<!--右侧主内容-->
			<div class="yui3-u-5-6">
				<div class="body userInfo">
					<ul class="sui-nav nav-tabs nav-large nav-primary ">
						<li class="active"><a href="#one" data-toggle="tab">基本资料</a></li>
						<li><a href="#two" data-toggle="tab">头像照片</a></li>
					</ul>
					<div class="tab-content ">
						<div id="one" class="tab-pane active">
							<form id="form-msg" class="sui-form form-horizontal">
								<div class="control-group">
									<label for="inputName" class="control-label">昵称：</label>
									<div class="controls">
										<input type="text" id="inputName" name="nickname" placeholder="昵称"
											value="<?php echo $userinfo['nickname']; ?>">
									</div>
								</div>

								<div class="control-group">
									<label for="inputGender" class="control-label">性别：</label>
									<div class="controls" >
										<label data-toggle="radio"
											class="radio-pretty inline <?php if(isset($userinfo['sex'])&&$userinfo['sex']==0): ?>checked<?php endif; ?>">
											<input type="radio" name="gender" value="0" <?php if(isset($userinfo['sex'])&&$userinfo['sex']==0): ?>checked="checked"<?php endif; ?>><span>男</span>
										</label>
										<label data-toggle="radio"
											class="radio-pretty inline <?php if(isset($userinfo['sex'])&&$userinfo['sex']==1): ?>checked<?php endif; ?>">
											<input type="radio" name="gender" value="1" <?php if(isset($userinfo['sex'])&&$userinfo['sex']==1): ?>checked="checked"<?php endif; ?>><span>女</span>
										</label>
									</div>
								</div>
								<div class="control-group">
									<label for="inputPassword" class="control-label">生日：</label>
									<div class="controls">
										<select id="select_year2" rel="<?php echo (isset($userinfo['birthday']['0']) && ($userinfo['birthday']['0'] !== '')?$userinfo['birthday']['0']:''); ?>"></select>年
										<select id="select_month2" rel="<?php echo (isset($userinfo['birthday']['1']) && ($userinfo['birthday']['1'] !== '')?$userinfo['birthday']['1']:''); ?>"></select>月
										<select id="select_day2" rel="<?php echo (isset($userinfo['birthday']['2']) && ($userinfo['birthday']['2'] !== '')?$userinfo['birthday']['2']:''); ?>"></select>日
									</div>
								</div>


								<div class="control-group">
									<label for="inputPassword" class="control-label">所在地：</label>
									<div class="controls">
										<div data-toggle="distpicker">
											<div class="form-group area">
												<select class="form-control" id="province1" data-province="<?php if(isset($userinfo['place']['0'])): ?><?php echo $userinfo['place']['0']; endif; ?>"></select>
											</div>
											<div class="form-group area">
												<select class="form-control" id="city1" data-city="<?php if(isset($userinfo['place']['1'])): ?><?php echo $userinfo['place']['1']; endif; ?>"></select>
											</div>
											<div class="form-group area">
												<select class="form-control" id="district1" data-district="<?php if(isset($userinfo['place']['2'])): ?><?php echo $userinfo['place']['2']; endif; ?>"></select>
											</div>
										</div>
									</div>
								</div>
								<div class="control-group">
									<label for="inputJob" class="control-label">职业：</label>

									<div class="controls"><span class="sui-dropdown dropdown-bordered select"><span
												class="dropdown-inner"><a role="button" data-toggle="dropdown" href="#"
													class="dropdown-toggle">
													<input name="job" type="hidden" data-rules="required"><i
														class="caret"></i><span>请选择</span></a>

												<ul id="menu2" role="menu" aria-labelledby="drop4"
													class="sui-dropdown-menu">
													<li role="presentation"><a role="menuitem" tabindex="-1"
															href="javascript:void(0);" value="bj">程序员</a></li>

												</ul>

											</span>
										</span>
									</div>

									<div class="controls"><span class="sui-dropdown dropdown-bordered select"><span
												class="dropdown-inner"><a role="button" data-toggle="dropdown" href="#"
													class="dropdown-toggle">
													<input name="job1" type="hidden" data-rules="required"><i
														class="caret"></i><span>请选择</span></a>
												<ul id="menu3" role="menu" aria-labelledby="drop4"
													class="sui-dropdown-menu">
													<li role="presentation"><a role="menuitem" tabindex="-1"
															href="javascript:void(0);" value="bj">程序员</a></li>
												</ul>
											</span>
										</span>
									</div>

									<div class="controls">
										<span class="sui-dropdown dropdown-bordered select">
											<span class="dropdown-inner"><a role="button" data-toggle="dropdown"
													href="#" class="dropdown-toggle">
													<input name="job2" type="hidden" data-rules="required"><i
														class="caret"></i><span>请选择</span></a>
												<ul id="menu4" role="menu" aria-labelledby="drop4"
													class="sui-dropdown-menu">
													<li role="presentation"><a role="menuitem" tabindex="-1"
															href="javascript:void(0);" value="bj">程序员</a></li>
												</ul>
											</span>
										</span>
									</div>


								</div>
								<div class="control-group">
									<label for="sanwei" class="control-label"></label>
									<div class="controls">
										<button type="submit" class="sui-btn btn-primary">修改</button>
									</div>
								</div>
							</form>
						</div>
						<div id="two" class="tab-pane">

							<div class="new-photo">
								<p>当前头像：</p>
								<div class="upload">
									<img id="imgShow_WU_FILE_0" width="100" height="100"
									<?php if(isset($userinfo['figure_url'])): ?>
										src="<?php echo $userinfo['figure_url']; ?>"
									<?php else: ?>
									src="/static/home/img/_/photo_icon.png"
									<?php endif; ?>
										alt="">
									<input type="file" id="up_img_WU_FILE_0" />
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript" src="/static/home/js/pages/main.js"></script>
<script>
	$(function () {
		var job2_id = null;
		$('#menu3 li').hide();
		$('#menu4 li').hide();
		class getjob {
			menu2() {
				$.get("http://www.pyg.com/usercenter/info/job",
					function (data, textStatus, jqXHR) {
						if (data.code != 200) {
							alert(data.msg);
						} else {
							for (var k in data.data) {
								if (<?php echo $userjob; ?> != [] && <?php echo $userjob; ?>[0] == data.data[k].id) {
									let str = `<input name='job2' type='hidden' data-rules='required'><i class='caret'></i><span>${data.data[k].job_name}</span>`;
									$('#menu2').prev().html(str);
								}
								$('#menu2 li:eq(0)').clone(true).appendTo('#menu2');
								$('#menu2 li a').eq(k).attr('value', data.data[k].id);
								$('#menu2 li a').eq(k).text(data.data[k].job_name);
							}
							$('#menu2 li').last().remove();
						}
					},
					"json"
				);
			}

			menu3(job_id) {
				$.get("http://www.pyg.com/usercenter/info/job", { 'pid': job_id },
					function (data, textStatus, jqXHR) {
						//console.log(data);
						if (data.code != 200) {
							alert(data.msg);
						} else {
							$('#menu3 li:eq(0)').show();
							for (var k in data.data) {
								if (<?php echo $userjob; ?>[1] == data.data[k].id) {
									let str = `<input name='job2' type='hidden' data-rules='required'><i class='caret'></i><span>${data.data[k].job_name}</span>`;
									$('#menu3').prev().html(str);
								}
								$('#menu3 li:eq(0)').clone(true).appendTo('#menu3');
								$('#menu3 li a').last().attr('value', data.data[k].id);
								$('#menu3 li a').last().text(data.data[k].job_name);
							}
							$('#menu3 li').first().remove();
						}
					},
					"json"
				);
			}

			menu4(job_id) {
				$.get("http://www.pyg.com/usercenter/info/job", { 'pid': job_id },
					function (data, textStatus, jqXHR) {
						//console.log(data);
						if (data.code != 200) {
							alert(data.msg);
						} else {
							$('#menu4 li:eq(0)').show();
							for (var k in data.data) {
								if (<?php echo $userjob; ?>[2] == data.data[k].id) {
									let str = `<input name='job2' type='hidden' data-rules='required'><i class='caret'></i><span>${data.data[k].job_name}</span>`;
									job2_id =data.data[k].id;
									$('#menu4').prev().html(str);
								}
								$('#menu4 li:eq(0)').clone(true).appendTo('#menu4');
								$('#menu4 li a').last().attr('value', data.data[k].id);
								$('#menu4 li a').last().text(data.data[k].job_name);
							}
							$('#menu4 li').first().remove();
						}
					},
					"json"
				);
			}
		}

var c = new getjob();
c.menu2();
if(<?php echo $userjob; ?>.length!=0){
	c.menu3(<?php echo $userjob; ?>[0]);
	c.menu4(<?php echo $userjob; ?>[1]);
}

		function job() {
			var job_id = null;
			var job1_id = null;
			var str = "<input name='job2' type='hidden' data-rules='required'><i class='caret'></i><span>请选择</span>";
			$("#menu2").on('click', 'a', function (e) {
				job_id = $(this).attr('value');
				if (!job_id) {
					return;
				}
				$('#menu3').prev().html(str);
				$('#menu4').prev().html(str);
				$.get("http://www.pyg.com/usercenter/info/job", { 'pid': job_id },
					function (data, textStatus, jqXHR) {
						//console.log(data);
						if (data.code != 200) {
							alert(data.msg);
						} else {
							$('#menu3 li:eq(0)').show();
							if (sessionStorage.getItem('menu3')) {
								$('#menu3 li').first().nextAll().remove();
							} else {
								sessionStorage.setItem('menu3', 1);
							}
							for (var k in data.data) {
								$('#menu3 li:eq(0)').clone(true).appendTo('#menu3');
								$('#menu3 li a').last().attr('value', data.data[k].id);
								$('#menu3 li a').last().text(data.data[k].job_name);
							}
							$('#menu3 li').first().remove();
						}
					},
					"json"
				);
			})
			$("#menu3").on('click', 'a', function (e) {
				job1_id = $(this).attr('value');
				if (!job1_id) {
					return;
				}
				$('#menu4').prev().html(str);
				$.get("http://www.pyg.com/usercenter/info/job", { 'pid': job1_id },
					function (data, textStatus, jqXHR) {
						//console.log(data);
						if (data.code != 200) {
							alert(data.msg);
						} else {
							$('#menu4 li:eq(0)').show();
							if (sessionStorage.getItem('menu4')) {
								$('#menu4 li').first().nextAll().remove();
							} else {
								sessionStorage.setItem('menu4', 1);
							}
							for (var k in data.data) {
								$('#menu4 li:eq(0)').clone(true).appendTo('#menu4');
								$('#menu4 li a').last().attr('value', data.data[k].id);
								$('#menu4 li a').last().text(data.data[k].job_name);
							}
							$('#menu4 li').first().remove();
						}
					},
					"json"
				);
			})

			$('#menu4').on('click','a',function(){
				job2_id = $(this).attr('value')
			})
		}
		job();

		$('#select_year2').change(function(){
			$(this).attr('rel',$(this).val());
		})

		$('#select_month2').change(function(){
			$(this).attr('rel',$(this).val());
		})

		$('#select_day2').change(function(){
			$(this).attr('rel',$(this).val());
		})

		$.ms_DatePicker({
			YearSelector: "#select_year2",
			MonthSelector: "#select_month2",
			DaySelector: "#select_day2"
		});
//#form-msg
		$('#form-msg').submit(function(e){
			e.preventDefault();
			let nickname = $('#inputName').val();
			let sex = $('[name="gender"]:checked').val();
			let year = $('#select_year2').attr('rel');
			let month = $('#select_month2').attr('rel');
			let day = $('#select_day2').attr('rel');
			let sheng  = $('#province1').children(':selected').val();
			let city  = $('#city1').children(':selected').val();
			let qu  = $('#district1').children(':selected').val();
			let birthday = '';
			if(year!=''||month!=''||day!=''){
				birthday = year+'/'+month+'/'+day
			}
			let place = sheng+'_'+city+'_'+qu;
			let job = job2_id?job2_id:'';
			let data = {
				nickname,
				sex,
				birthday,
				place,
				job
			}
			$.ajax({
				type: "post",
				url: "http://www.pyg.com/usercenter/info/edit",
				data:data,
				dataType: "json",
				success: function (response) {
					console.log(response)
					if(response.code!=200){
						layer.msg('修改失败', {icon: 5});
					}else{
						location.reload(true)
					}
				}
			});
		})

		$('#up_img_WU_FILE_0').change(function(){
			console.log(this.files[0])
			var formdata = new FormData();
			formdata.append('figure_url',this.files[0]);
			$.ajax({
				type: "post",
				url: "http://www.pyg.com/usercenter/info/editheader",
				data: formdata,
				"contentType":false,
                "processData":false,
				dataType: "json",
				success: function (response) {
					console.log(response)
					if(response.code!=200){
						layer.msg('修改失败,'+response.msg, {icon: 5});
					}else{
						let src = response.data;
						$('#imgShow_WU_FILE_0').attr('src',src);
						$('.person-photo img').attr('src',src);
					}
				}
			});
		})
	});
</script>
	<!-- 底部栏位 -->
	<!--页面底部-->
	<div class="clearfix footer">
		<div class="py-container">
			<div class="footlink">
				<div class="Mod-service">
					<ul class="Mod-Service-list">
						<li class="grid-service-item intro  intro1">

							<i class="serivce-item fl"></i>
							<div class="service-text">
								<h4>正品保障</h4>
								<p>正品保障，提供发票</p>
							</div>

						</li>
						<li class="grid-service-item  intro intro2">

							<i class="serivce-item fl"></i>
							<div class="service-text">
								<h4>正品保障</h4>
								<p>正品保障，提供发票</p>
							</div>

						</li>
						<li class="grid-service-item intro  intro3">

							<i class="serivce-item fl"></i>
							<div class="service-text">
								<h4>正品保障</h4>
								<p>正品保障，提供发票</p>
							</div>

						</li>
						<li class="grid-service-item  intro intro4">

							<i class="serivce-item fl"></i>
							<div class="service-text">
								<h4>正品保障</h4>
								<p>正品保障，提供发票</p>
							</div>

						</li>
						<li class="grid-service-item intro intro5">

							<i class="serivce-item fl"></i>
							<div class="service-text">
								<h4>正品保障</h4>
								<p>正品保障，提供发票</p>
							</div>

						</li>
					</ul>
				</div>
				<div class="clearfix Mod-list">
					<div class="yui3-g">
						<div class="yui3-u-1-6">
							<h4>购物指南</h4>
							<ul class="unstyled">
								<li>购物流程</li>
								<li>会员介绍</li>
								<li>生活旅行/团购</li>
								<li>常见问题</li>
								<li>购物指南</li>
							</ul>

						</div>
						<div class="yui3-u-1-6">
							<h4>配送方式</h4>
							<ul class="unstyled">
								<li>上门自提</li>
								<li>211限时达</li>
								<li>配送服务查询</li>
								<li>配送费收取标准</li>
								<li>海外配送</li>
							</ul>
						</div>
						<div class="yui3-u-1-6">
							<h4>支付方式</h4>
							<ul class="unstyled">
								<li>货到付款</li>
								<li>在线支付</li>
								<li>分期付款</li>
								<li>邮局汇款</li>
								<li>公司转账</li>
							</ul>
						</div>
						<div class="yui3-u-1-6">
							<h4>售后服务</h4>
							<ul class="unstyled">
								<li>售后政策</li>
								<li>价格保护</li>
								<li>退款说明</li>
								<li>返修/退换货</li>
								<li>取消订单</li>
							</ul>
						</div>
						<div class="yui3-u-1-6">
							<h4>特色服务</h4>
							<ul class="unstyled">
								<li>夺宝岛</li>
								<li>DIY装机</li>
								<li>延保服务</li>
								<li>品优购E卡</li>
								<li>品优购通信</li>
							</ul>
						</div>
						<div class="yui3-u-1-6">
							<h4>帮助中心</h4>
							<img src="/static/home/img/wx_cz.jpg">
						</div>
					</div>
				</div>
				<div class="Mod-copyright">
					<ul class="helpLink">
						<li>关于我们<span class="space"></span></li>
						<li>联系我们<span class="space"></span></li>
						<li>关于我们<span class="space"></span></li>
						<li>商家入驻<span class="space"></span></li>
						<li>营销中心<span class="space"></span></li>
						<li>友情链接<span class="space"></span></li>
						<li>关于我们<span class="space"></span></li>
						<li>营销中心<span class="space"></span></li>
						<li>友情链接<span class="space"></span></li>
						<li>关于我们</li>
					</ul>
					<p>地址：北京市昌平区建材城西路金燕龙办公楼一层 邮编：100096 电话：400-618-4000 传真：010-82935100</p>
					<p>京ICP备08001421号京公网安备110108007702</p>
				</div>
			</div>
		</div>
	</div>
	<!--页面底部END-->
	<!--侧栏面板开始-->
	<div class="J-global-toolbar">
		<div class="toolbar-wrap J-wrap">
			<div class="toolbar">
				<div class="toolbar-panels J-panel">

					<!-- 购物车 -->
					<div style="visibility: hidden;"
						class="J-content toolbar-panel tbar-panel-cart toolbar-animate-out">
						<h3 class="tbar-panel-header J-panel-header">
							<a href="" class="title"><i></i><em class="title">购物车</em></a>
							<span class="close-panel J-close" onclick="cartPanelView.tbar_panel_close('cart');"></span>
						</h3>
						<div class="tbar-panel-main">
							<div class="tbar-panel-content J-panel-content">
								<div id="J-cart-tips" class="tbar-tipbox hide">
									<div class="tip-inner">
										<span class="tip-text">还没有登录，登录后商品将被保存</span>
										<a href="#none" class="tip-btn J-login">登录</a>
									</div>
								</div>
								<div id="J-cart-render">
									<!-- 列表 -->
									<div id="cart-list" class="tbar-cart-list">
									</div>
								</div>
							</div>
						</div>
						<!-- 小计 -->
						<div id="cart-footer" class="tbar-panel-footer J-panel-footer">
							<div class="tbar-checkout">
								<div class="jtc-number"> <strong class="J-count" id="cart-number">0</strong>件商品 </div>
								<div class="jtc-sum"> 共计：<strong class="J-total" id="cart-sum">¥0</strong> </div>
								<a class="jtc-btn J-btn" href="#none" target="_blank">去购物车结算</a>
							</div>
						</div>
					</div>

					<!-- 我的关注 -->
					<div style="visibility: hidden;" data-name="follow"
						class="J-content toolbar-panel tbar-panel-follow">
						<h3 class="tbar-panel-header J-panel-header">
							<a href="#" target="_blank" class="title"> <i></i> <em class="title">我的关注</em> </a>
							<span class="close-panel J-close"
								onclick="cartPanelView.tbar_panel_close('follow');"></span>
						</h3>
						<div class="tbar-panel-main">
							<div class="tbar-panel-content J-panel-content">
								<div class="tbar-tipbox2">
									<div class="tip-inner"> <i class="i-loading"></i> </div>
								</div>
							</div>
						</div>
						<div class="tbar-panel-footer J-panel-footer"></div>
					</div>

					<!-- 我的足迹 -->
					<div style="visibility: hidden;"
						class="J-content toolbar-panel tbar-panel-history toolbar-animate-in">
						<h3 class="tbar-panel-header J-panel-header">
							<a href="#" target="_blank" class="title"> <i></i> <em class="title">我的足迹</em> </a>
							<span class="close-panel J-close"
								onclick="cartPanelView.tbar_panel_close('history');"></span>
						</h3>
						<div class="tbar-panel-main">
							<div class="tbar-panel-content J-panel-content">
								<div class="jt-history-wrap">
									<ul>
										<!--<li class="jth-item">
											<a href="#" class="img-wrap"> <img src="../../.../portal/img/like_03.png" height="100" width="100" /> </a>
											<a class="add-cart-button" href="#" target="_blank">加入购物车</a>
											<a href="#" target="_blank" class="price">￥498.00</a>
										</li>
										<li class="jth-item">
											<a href="#" class="img-wrap"> <img src="../../../portal/img/like_02.png" height="100" width="100" /></a>
											<a class="add-cart-button" href="#" target="_blank">加入购物车</a>
											<a href="#" target="_blank" class="price">￥498.00</a>
										</li>-->
									</ul>
									<a href="#" class="history-bottom-more" target="_blank">查看更多足迹商品 &gt;&gt;</a>
								</div>
							</div>
						</div>
						<div class="tbar-panel-footer J-panel-footer"></div>
					</div>

				</div>

				<div class="toolbar-header"></div>

				<!-- 侧栏按钮 -->
				<div class="toolbar-tabs J-tab">
					<div onclick="cartPanelView.tabItemClick('cart')" class="toolbar-tab tbar-tab-cart" data="购物车"
						tag="cart">
						<i class="tab-ico"></i>
						<em class="tab-text"></em>
						<span class="tab-sub J-count " id="tab-sub-cart-count">0</span>
					</div>
					<div onclick="cartPanelView.tabItemClick('follow')" class="toolbar-tab tbar-tab-follow" data="我的关注"
						tag="follow">
						<i class="tab-ico"></i>
						<em class="tab-text"></em>
						<span class="tab-sub J-count hide">0</span>
					</div>
					<div onclick="cartPanelView.tabItemClick('history')" class="toolbar-tab tbar-tab-history"
						data="我的足迹" tag="history">
						<i class="tab-ico"></i>
						<em class="tab-text"></em>
						<span class="tab-sub J-count hide">0</span>
					</div>
				</div>

				<div class="toolbar-footer">
					<div class="toolbar-tab tbar-tab-top"> <a href="#"> <i class="tab-ico  "></i> <em
								class="footer-tab-text">顶部</em> </a> </div>
					<div class="toolbar-tab tbar-tab-feedback"> <a href="#" target="_blank"> <i class="tab-ico"></i> <em
								class="footer-tab-text ">反馈</em> </a> </div>
				</div>

				<div class="toolbar-mini"></div>

			</div>

			<div id="J-toolbar-load-hook"></div>

		</div>
	</div>
	<!--购物车单元格 模板-->
	<script type="text/template" id="tbar-cart-item-template">
		<div class="tbar-cart-item" >
			<div class="jtc-item-promo">
				<em class="promo-tag promo-mz">满赠<i class="arrow"></i></em>
				<div class="promo-text">已购满600元，您可领赠品</div>
			</div>
			<div class="jtc-item-goods">
				<span class="p-img"><a href="#" target="_blank"><img src="{2}" alt="{1}" height="50" width="50" /></a></span>
				<div class="p-name">
					<a href="#">{1}</a>
				</div>
				<div class="p-price"><strong>¥{3}</strong>×{4} </div>
				<a href="#none" class="p-del J-del">删除</a>
			</div>
		</div>
	</script>
	<!--侧栏面板结束-->

</body>

</html>
<script>
	$(function () {
		$('#logout').click(function () {
			$.get("http://www.pyg.com/user/logout",
				function (data, textStatus, jqXHR) {
					if (data.code == 200) {
						location.href = 'http://www.pyg.com/user/login';
					} else {
						layer.msg('退出失败', { icon: 5 });
					}
				},
				"json"
			);
		})

		
	})
</script>