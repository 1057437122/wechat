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
	// $wechatObj->test();
	$wechatObj->respondMsg();
	// $wechatObj->sendMsg();
	
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
	public function respondMsg(){
		if(IS_DEBUG){
			$postStr="<xml>
						<ToUserName><![CDATA[toUser]]></ToUserName>
						<FromUserName><![CDATA[fromUser]]></FromUserName> 
						<CreateTime>1348831860</CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[1]]></Content>
						<MsgId>1234567890123456</MsgId>
						</xml>";
		}else{
			$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		}
		if(!empty($postStr) && $this->checkSignature()){
			$postObj=simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);//get the object
			if($postObj->MsgType=='event'){
				if($postObj->Event=='subscribe'){//first subscribe
					$subscribePage=get_pages('meta_key=showOnFirstSub&numberposts=10');
					$leftPostNo=10-count($subscribePage);
					$subscribePost=get_posts('meta_key=showOnFirstSub&numberposts='.$leftPostNo);
					$subscribeRe=array_merge($subscribePage,$subscribePost);
					$this->sendPhMsg($postObj->FromUserName,$postObj->ToUserName,$subscribeRe);
				}
			}else{
				$this->sendPhMsg($postObj->FromUserName,$postObj->ToUserName,$this->getData($postObj->Content)); 
			}
		}
	}
	public function autoReply(){
		
	}
	
	private function sendPhMsg($fromUserName,$toUserName,$contentData){
		if($contentData==''){
			return 'em';
		}
		
        $headerTpl = "<ToUserName><![CDATA[%s]]></ToUserName>
			        <FromUserName><![CDATA[%s]]></FromUserName>
			        <CreateTime>%s</CreateTime>
			        <MsgType><![CDATA[%s]]></MsgType>
			        <ArticleCount>%s</ArticleCount>";
			        
		$itemTpl=  "<item>
					<Title><![CDATA[%s]]></Title> 
					<Description><![CDATA[%s]]></Description>
					<PicUrl><![CDATA[%s]]></PicUrl>
					<Url><![CDATA[%s]]></Url>
					</item>";
		$time=time();
		$itemStr='';
		$mediaCount=0;
		if(count($contentData)==1 && strlen($contentData[0]->post_content)<1000){//if only one ,just show in one ph message
			$itemStr=sprintf($itemTpl,$contentData[0]->post_title,$contentData[0]->post_content,$this->getThumbnail($contentData[0]->ID),$contentData[0]->guid);
			$mediaCount=1;
		}elseif(empty($contentData) || empty($contentData[0])){
			$itemStr=sprintf($itemTpl,'Nothing~','没有找到搜索内容，请重新输入关键词！',$this->getThumbnail(0),'');
			$mediaCount=1;
		}else{
			foreach($contentData as $conObj){
				$tmp_itm=sprintf($itemTpl,$conObj->post_title,trim(strip_tags(substr($conObj->post_content,0,120))),$this->getThumbnail($conObj->ID),$conObj->guid);
				$itemStr.=$tmp_itm;
				$mediaCount++;
				if($mediaCount>=9){
					break;
				}
			}
		}
		$msgType='news';
		$headerStr = sprintf($headerTpl, $fromUserName, $toUserName, $time, $msgType, $mediaCount);
		$resultStr ="<xml>".$headerStr."<Articles>".$itemStr."</Articles></xml>";
		echo $resultStr;
	}
	private function getThumbnail($post_id){//get the thumbnail if exist else get a random pic under the plugin directory
		$thumbnailUrlArr=wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'thumbnail');
		$thumbnailUrl=$thumbnailUrlArr[0];
		if(!$thumbnailUrlArr[3]){
			$randId=rand(0,9);
			$thumbnailUrl=plugins_url().'/mywechat/img/'.$randId.'.jpg';
		}
		return $thumbnailUrl;
	}
	private function getData($_keyword){
		$args=array(
			'post_type' => 'post',
			'orderby' => 'date',
			'post_status' => 'publish',
			'order'=> 'DESC',
			'posts_per_page' => -1
		);
		global $wpdb;
		$sql="select ID,post_content,post_title,guid from $wpdb->posts where (post_content like '%".trim($_keyword)."%' or post_title like '%".$_keyword."%') and post_status='publish' and (post_type='page' or post_type='post') order by post_date limit 0,9";
		
		$res=$wpdb->get_results( $sql );
		return $res;
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
	public function test(){
		echo $this->getThumbnail(18);
	}
	private function sendPhMsgTest($fromUserName,$toUserName){
		$headerTpl = "<ToUserName><![CDATA[%s]]></ToUserName>
			        <FromUserName><![CDATA[%s]]></FromUserName>
			        <CreateTime>%s</CreateTime>
			        <MsgType><![CDATA[%s]]></MsgType>
			        <ArticleCount>%s</ArticleCount>";
			        
		$itemTpl=  "<item>
					<Title><![CDATA[%s]]></Title> 
					<Description><![CDATA[%s]]></Description>
					<PicUrl><![CDATA[%s]]></PicUrl>
					<Url><![CDATA[%s]]></Url>
					</item>";
		$time=time();
		$msgType='news';
		$headerStr=sprintf($headerTpl,$fromUserName,$toUserName,$time,$msgType,2);
		$itemStr=sprintf($itemTpl,'nice','what are you doing','http://b264.photo.store.qq.com/psb?/c8feaece-09cc-409b-bb88-5df3183c9d12/e0WlAK*JI1w8m1ai92PonHDI5FBreSByI6b8oJBMsUM!/b/dCvhX53qGgAA&bo=kADiAAAAAAABAFU!&rf=viewer_4','http://www.baidu.com');
		$itemStr.=$itemStr;
		$resultStr ="<xml>".$headerStr."<Articles>".$itemStr."</Articles></xml>";
		echo $resultStr;
	}
}