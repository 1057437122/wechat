<?php if(! defined('BASEPATH')) exit('No Direct script access allowed');
/**
  * wechat core class 
  */

class Wecore
{
	public function __construct(){
		$postObj='';//contains all the information of the message user sent
	}
	public function init(){
		$postStr=$GLOBALS['HTTP_RAW_POST_DATA'];
		if(!empty($postStr)){
			$this->postObj=simplexml_load_string($postStr,'SimpleXMLElement',LIBXML_NOCDATA);
		}
	}
	public function response($msg=FALSE){
		if($msg===FALSE){
			$ret='welcome to subscribe me~~~';
		}else{
			$ret=$msg;
		}
		$this->send_text_msg($ret);
	}
	public function send_text_msg($msg){//send text message
		$textTpl = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[text]]></MsgType>
			<Content><![CDATA[%s]]></Content>
			<FuncFlag>0</FuncFlag>
			</xml>";
		$time=time();
		$resultStr=sprintf($textTpl,$this->postObj->FromUserName,$this->postObj->ToUserName,$time,$msg);
		echo $resultStr;
	}
	public function get_msg(){//get the 
	}
	public function tst(){
		echo 'nice';
	}
}
/*End of file Wecore.php */
/*location:application/libraries */
