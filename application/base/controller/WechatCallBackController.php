<?php
namespace app\base\controller;
use think\Controller;
use EasyWeChat\Foundation\Application;
class WechatCallBackController extends Controller{

	public function server_config(){
		$app = app();
		$server = $app->server;
		$server->setMessageHandler(function ($message) {
		    // $message->FromUserName // 用户的 openid
		    // $message->MsgType // 消息类型：event, text....
		    return "您好！欢迎关注我!";
		});
		$response = $server->serve();
		$response->send();
	}
}