<?php
/**
  * WeChat Interface
  * 
  * 
  *   
  */
global $token;

define('IS_DEBUG', false);

$wechatObj = new wecore($token);

$valid=$wechatObj->valid();

if($valid){
	
	$wechatObj->sendMsg();
	
}
class wecore{

	private $token;
	public function __construct($_token){
		$this->token=$_token;
	}
	public function sendMsg(){
		$postStr=$GLOBALS["HTTP_RAW_POST_DATA"];
		if(!empty($postStr) && $this->checkSignature()){
			$postObj=simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$textTpl = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[text]]></MsgType>
			<Content><![CDATA[%s]]></Content>
			<FuncFlag>0</FuncFlag>
			</xml>";
			$time=time();
			$resultStr=sprintf($textTpl,$postObj->FromUserName,$postObj->ToUserName,$time,'nice');
			echo $resultStr;
		}
	}
	private function getData($_keyword){
		
	}
	public function valid(){
		if(isset($_GET["echostr"])){
	    	$echoStr = $_GET["echostr"];
	    }
	    //valid signature , option
	    if($this->checkSignature()){
	    	if(isset($echoStr) && $echoStr!=''){
	    		echo $echoStr;
	    		exit;
	    	}
	    	return true;
	    }else{
	    	return false;
	    }
	}
	
	private function checkSignature(){
		if(IS_DEBUG){
			return true;
		}
		$signature =isset($_GET["signature"])?$_GET["signature"]:'';
		$timestamp =isset($_GET["timestamp"])?$_GET["timestamp"]:'';
        $nonce = isset($_GET["nonce"])?$_GET["nonce"]:'';	
        		
		$token = $this->token;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr,SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}