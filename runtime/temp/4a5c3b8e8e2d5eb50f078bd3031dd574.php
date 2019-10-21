<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:68:"F:\www\pyg\public/../application/home\view\login_register\login.html";i:1570422435;s:59:"F:\www\pyg\application\home\view\Common\login_register.html";i:1570420756;}*/ ?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<link rel="stylesheet" type="text/css" href="/static/home/css/all.css" />
	<script type="text/javascript" src="/static/home/js/all.js"></script>
	<script src="/static/home/js/plugins/layer/layer.js"></script>
</head>

<body>
		<title>品优购，欢迎登录</title>

<link rel="stylesheet" type="text/css" href="/static/home/css/pages-login.css" />
<script type="text/javascript" src="/static/home/js/pages/validate.js"></script>
<script type="text/javascript" src="/static/home/js/pages/login.js"></script>
<div class="login-box">
	<!--head-->
	<div class="py-container logoArea">
		<a href="" class="logo"></a>
	</div>
	<!--loginArea-->
	<div class="loginArea">
		<div class="py-container login">
			<div class="loginform">
				<ul class="sui-nav nav-tabs tab-wraped">
					<li>
						<a href="#index" data-toggle="tab">
							<h3>扫描登录</h3>
						</a>
					</li>
					<li class="active">
						<a href="#profile" data-toggle="tab">
							<h3>账户登录</h3>
						</a>
					</li>
				</ul>
				<div class="tab-content tab-wraped">
					<div id="index" class="tab-pane">
						<p>二维码登录，暂为官网二维码</p>
						<img src="/static/home/img/wx_cz.jpg" />
					</div>
					<div id="profile" class="tab-pane  active">
						<form class="sui-form">
							<div class="input-prepend"><span class="add-on loginname"></span>
								<input id="prependedInput" type="text" placeholder="邮箱/用户名/手机号" class="span2 input-xfat"
									name="phone">
							</div>
							<div class="input-prepend password"><span class="add-on loginpwd"></span>
								<input id="prependedInput" type="password" placeholder="请输入密码" class="span2 input-xfat"
									name="password">
							</div>
							<div class="setting">
								<label class="checkbox inline">
									<input name="remlogin" type="checkbox" value="2">
									自动登录
								</label>
								<span class="forget">忘记密码？</span>
							</div>
							<div class="logined">
								<input type="submit" value="登录" class="sui-btn btn-block btn-xlarge btn-danger">
							</div>
						</form>
						<div class="otherlogin">
							<div class="types">
								<ul>
									<!-- <li>
										<a href="/plugins/tencent/qq/example/oauth/index.php" id='qq1'>
											<img src="/static/home/img/qq.png" width="35px" height="35px" />
										</a>
									</li> -->
									<li>
										<a href="javascript:;" id='qq'>
											<img src="/static/home/img/qq.png" width="35px" height="35px" />
										</a>
									</li>
									<li><img src="/static/home/img/sina.png" /></li>
									<li>
										<a href="javascript:;" id='zfb'>
											<img src="/static/home/img/ali.png" />
										</a>
									</li>
									<li><img src="/static/home/img/weixin.png" /></li>
								</ul>
							</div>
							<span class="register"><a href="register.html" target="_blank">立即注册</a></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
		<!--foot-->
		<div class="py-container copyright">
			<ul>
				<li>关于我们</li>
				<li>联系我们</li>
				<li>联系客服</li>
				<li>商家入驻</li>
				<li>营销中心</li>
				<li>手机品优购</li>
				<li>销售联盟</li>
				<li>品优购社区</li>
			</ul>
			<div class="address">地址：北京市昌平区建材城西路金燕龙办公楼一层 邮编：100096 电话：400-618-4000 传真：010-82935100</div>
			<div class="beian">京ICP备08001421号京公网安备110108007702
			</div>
		</div>
	</div>
</body>

</html>