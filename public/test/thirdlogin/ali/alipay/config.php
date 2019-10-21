<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2019092267734583",
		//编码格式
		'charset' => "UTF-8",
		//签名方式
		'sign_type'=>"RSA2",
		//scope auth_user获取用户信息; auth_base静默授权
		'scope' => 'auth_user',
		//跳转地址
		'redirect_url' => 'http://'.$_SERVER['HTTP_HOST'].'/home/login/alicallback',
        //支付宝授权地址 沙箱环境
//        'oauth_url' => 'https://openauth.alipaydev.com/oauth2/publicAppAuthorize.htm',
        //支付宝授权地址 正式环境
         'oauth_url' => 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm',
        //支付宝网关 沙箱环境
//        'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",
        //支付宝网关 正式环境
        'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//商户私钥
		'merchant_private_key' => "MIIEpAIBAAKCAQEAut8KE0I+/w8+XrJVGnEsLPCVRSqclpji8k3hn5p1s0bjOegCemULLgQ4PFCeY/K7iCE7YrfUl7YGjym7U9cUdx5LY23g6tCXtfGkQQAtY+JvV+u1vOwsh6e17h5F5PGug8WGjfAOS3plnvs/eykrOsQy+ZqnErq9NbzG5Ydyb9kQY5OaPRzYfSyfG4vB2jUnaSrU2eZCtfA+ZSu4Z4qGgCoi1WudaSdGgN5QtX0fTztQH8VEgApWStdmQi+D3g3uSsm/M8Ss4ZG1qVrLayW3lcDQ4KM2SdRY+2Ytf5U2V+kiRY5DeC8KJmCd0F4g6A0KzTzM7MjJxUIiDJhhWjQZIQIDAQABAoIBABYft+Kl0i2CUYEGnfq+cVt0tEd3DwjpWt0TCWZK9CpgdyBw7nItKlCtYTcK2GW+5CLuxEgguYOookgqyanYaezcYlKIPLuwLHX3ANpNOhJ8SXBgUKjoUTFSUsC5Rs8fKekh5pdBV3/qIsPavR5ItnyDpAFXJPabszD8g5PDIHJLTbH6knRGwTez0P9D2dk2Q/yYH2+qghNQojDJozkqY0J2+gnwbOeNuze/LgaY7+9BJtNOrfYq4HY+sdPJbrsjEI6E6Z0Ir96LOTqfqw9dxipnQPOVEU233pUQjXQoHNy+JmPaGqDS49NZMfDxLN27a++rToypnUQed6WAh4OuJ1ECgYEA6n91lC4WADBrrWUTkwuYXcQFp/kZ0Flmvy63an4qHAl5IEi8HHvUynRgICZ2dQysihK634zlhey9Iag1ZVzcYH82A5+mDuWH9pSHDzoQKUZpero1lFhtRsuN5tdIt2hzjLkK9s6A+R7QQVzPlXiWqLfVJVEXg7+RMQPbXiVTPkUCgYEAzAGa5DY/6HwZT9eAXJFOUk+Ftu6Go9u0kgFfazd8clqjiCURu1O25IZcA/VO+GIhTA6kAVibAq3o6BqDSneABgn0ATKUYC3jjAMDhVJY+lv2yNerzARvNevn5eVRpGvYmDiosuFjviQR6zYvKl7KfuJjZCton4spFIlgO6b7ey0CgYEAphd/XWRDGwEw01DbS3SUCB32b6IlYYhhGRrquNgB1Xf0PiSmcHpZHsjM6Ri1IHTIpMdda0etrm0fDP7KSzA5u3N++5QRl02GPuW2v9c9aS7BOTc5CgiT5ef5az6i951Y8pyCIovjmA/2K8WkFleiRoBmzRah1CRUn2X+87D2RA0CgYEAusudb+jci5tV8e1480l5VZTK8r1lOxQpOqdXH20m3e5wXnDS05vLk2QTTOyI6pWvt1yQf0sKZGGpKR1dqgnRh7YFXjNZ+NcLy1/XEXRdVKBwT2ZrP9uvmMfxBmf7YXn+USNQFLqcAbY1UhHDDiDNeXEBYO7+VVeuvccr/nLfQeECgYAX1bEMZ1S2F95YN0iHTWonlIxPUDoc35KsS4FSZTQXcUDmiCZIWesz9l5ET5dDVVJm6koBnue9+Gc/FiOvduCO9DxuT1EJo4psEPVG2JWSVKLMTpdjup6zj8HNcZFy0YYcWsuVdkau/eeUhrgshoLanatHa/TQSvlqRaCvaFuzhw==",

);