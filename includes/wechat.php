<?php
/**
  * wechat php test
  */
#these for line is just for signature the website so ..
#define('TOKEN','leepine');
#$we=new Wechat();
#$we->valid();
#$we->responseMsg();
class Wechat extends CI_Controller
{
	public function __construct(){
		parent::__construct();
#		$this->load->helper('url');
		$this->load->library('wecore');
		$this->welcome='';
	}
	public function set_welcome(){
		$this->load->model('autoresponse_model');
		$sql='select title,introduce from rich_autoresponse where isactive="1"';
		$query=$this->autoresponse_model->db->query($sql);
		$welcome='欢迎关注Leepine，';
		foreach($query->result_array() as $item){
			$welcome.='回复"'.$item['title'].'"获得'.$item['introduce'].";";
		}
		$welcome.='回复"jy +你的建议"向本堂提建议，注意jy后面有个空格';
		return $welcome;
	}
	public function valid1()
    {
		$test=$this->set_welcome();
		echo $test;
        // $echoStr = $_GET["echostr"];

        //valid signature , option
        // if($this->checkSignature()){
        	// echo $echoStr;
        	// exit;
        // }
		// $request='jy 我是 一兵';
		// $pos=strpos($request,' ');//get the position of the blank
		// $pre=substr($request,0,$pos);
		// print $pre;
		// if($pre==='jy'){//suggestions
			// $suggestion=trim(substr($request,$pos));
		// $username='oa918jtUwbNzzE3G-6YYIPvhXJbm';
			// $this->load->model('suggestion_model');
			// if($this->suggestion_model->save_suggestion($suggestion,$username)){
				// $msg=array('answer'=>'we got it,thx');
			// }
		// }
		// print_r($msg);
    }

    public function valid()
    {
		$this->welcome=$this->set_welcome();
	    $this->wecore->init();
		if($this->wecore->postObj->MsgType=='text'){//text type
			$request=trim((string)$this->wecore->postObj->Content);
			$pos=strpos($request,' ');//get the position of the blank
			$pre=substr($request,0,$pos);//get the prefix of the request
			if($pre==='jy'){//suggestions
				$suggestion=trim(substr($request,$pos));
				#$username=$this->wecore->postObj->FromUserName;//this caused why 
				$username='cannot insert';
				$time=time();
				$this->load->model('suggestion_model');
				if($this->suggestion_model->save_suggestion($suggestion,$username,$time)){
					$msg=array('answer'=>'您的建议已经提交，感谢您的参与，愿神祝福你');
				}else{
					$msg=array('answer'=>$this->welcome);
				}
			}else{//auto response
				$this->load->model('autoresponse_model');
				if(!$msg=$this->autoresponse_model->get_answer($request)){
					$msg=array('answer'=>$this->welcome);
				}
			}
		}else{//other type
			$msg=array('answer'=>$this->welcome);
		}
	    $this->wecore->response($msg['answer']);
    }
		
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}
/*End of file Wechat.php */
/*location:application/controller */
