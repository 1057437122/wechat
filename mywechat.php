<?php 
/*
Plugin Name:mywechat
Plugin URI:http://tech.leepine.com
Author:Leez
Description: this is used to manage your wechat platform
Author URI:http://tech.leepine.com
*/

define(WECHAT_OPTION,'wechat_options');
define(MY_LANG,'leez');
function mywechat_admin(){
	if(!isset($_GET['action']) && isset($_POST['wechataccess'])){
		if(wp_verify_nonce($_POST['_wpnonce'],'wechat_admin_option_update')){
			update_option('wechat_token',stripslashes($_POST['wechat_token']));
			update_option('wechat_access_url',stripslashes(home_url().'/?'.$_POST['wechat_token']));
			if(isset($_POST['enable_custom_menu']) && $_POST['enable_custom_menu']=='enable_custom_menu'){
				update_option('customMenuAcc',1);
				$customMenuAcc=get_option('customMenuAcc');
				if(isset($_POST['custom_menu_appid']) && !empty($_POST['custom_menu_appid']) && isset($_POST['custom_menu_appsecret']) && !empty($_POST['custom_menu_appsecret'])){
					update_option('custom_menu_appid',stripslashes($_POST['custom_menu_appid']));
					update_option('custom_menu_appsecret',stripslashes($_POST['custom_menu_appsecret']));
				}
			}else{
				update_option('customMenuAcc',0);
			}
			echo '<div class="updated"><p>'.__('Success!You changes were successfully saved').'</p></div>';
		}else{
			echo '<div class="error"><p>'.__('Whoops...').'</p></div>';
		}
	}
	if(isset($_GET['action']) && isset($_POST['custommenusettings'])){
		if(wp_verify_nonce($_POST['_wpnonce'],'wechat_custom_menu_conf')){
			$ret_json='{"button":[';
			foreach($_POST['item'] as $items){
				if($items['type']=='click'){
					$tmp='{"type":"click","name":"'.$items['name'].'","key":"'.$items['key'].'"},';
					$ret_json.=$tmp;
				}elseif($items['type']=='view'){
					$tmp='{"type":"view","name":"'.$items['name'].'","url":"'.$items['key'].'"},';
					$ret_json.=$tmp;
				}else{//type is submenu
					$subret_json='{"name":"'.$items['name'].'","sub_button":[';
					foreach($items['submenu'] as $submenuItem){
						if($submenuItem['type']=='click'){
							$tmp='{"type":"click","name":"'.$submenuItem['name'].'","key":"'.$submenuItem['key'].'"},';
							$subret_json.=$tmp;
						}else{
							$tmp='{"type":"view","name":"'.$submenuItem['name'].'","url":"'.$submenuItem['key'].'"},';
							$subret_json.=$tmp;
						}
					}
					$subret_json=substr($subret_json,0,-1);
					$subret_json.=']},';
					$ret_json.=$subret_json;
				}
				
			}
			$ret_json=substr($ret_json,0,-1);
			$ret_json.=']}';
			// echo $ret_json;
			update_option('customMenuItemJson',$ret_json);
			
			post_custom_item($ret_json);
		}
	}
?>
<!--
admin setting about wechat
-->
<div class="wrap">
<?php $locations_self_menu = ( isset( $_GET['action'] ) && 'customMenu' == $_GET['action'] ) ? true : false;?>
	<h2 class="nav-tab-wrapper">
		<a href="<?php echo admin_url('admin.php').'?page='.WECHAT_OPTION;?>" class="nav-tab <?php if ( ! isset( $_GET['action'] ) || isset( $_GET['action'] ) && 'customMenu' != $_GET['action'] ) echo ' nav-tab-active'; ?>"><?php _e('Basic Setting');?></a>
		<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'customMenu' ), admin_url( 'admin.php' ).'?page='.WECHAT_OPTION ) ); ?>" class="nav-tab <?php if($locations_self_menu){ echo 'nav-tab-active'; }?>"><?php _e('Custom Menu');?></a>
	</h2>
	<?php screen_icon();?>
	<?php if(!$locations_self_menu):?>
	<h2><?php _e('Wechat Configuration');?></h2>
	<form action="" method="post" id="wechat_conf_form">
		<table class="form-table">
		<tr class="">
			<th><label for="Wechat_token">Token</label></th>
			<td><input type="text" name="wechat_token" id="wechat_token" value="<?php echo esc_attr(get_option('wechat_token'));?>"/></td>
		</tr>
		
		<tr class="">
			<th><label for="Wechat_access_url"><?php _e('Url');?></label></th>
			<td><label><?php echo esc_attr(get_option('wechat_access_url'));?></label></td>
		</tr>
		<?php $customMenuAcc=get_option('customMenuAcc'); ?>
		<tr>
			<th><input type="checkbox" name="enable_custom_menu" value="enable_custom_menu" <?php if($customMenuAcc){echo 'checked';}?>><?php _e('Enable Custom Menu');?></th>
		</tr>
		
		<?php if($customMenuAcc): ?>
		
		<tr class="">
			<th><label for="custom_menu_appid"><?php _e('Custom menu Access');?></label></th>
			<td><input type="text" name="custom_menu_appid" id="custom_menu_appid" value="<?php echo esc_attr(get_option('custom_menu_appid'));?>"/></td>
		</tr>
		
		<tr class="">
			<th><label for="custom_menu_appsecret"><?php _e('Custom menu AppSecret');?></label></th>
			<td><input type="text" name="custom_menu_appsecret" id="custom_menu_appsecret" value="<?php echo esc_attr(get_option('custom_menu_appsecret'));?>"/></td>
		</tr>
		<?php endif; ?>
		<tr>
			<td>
				<input class="button button-primary" type="submit" name="wechataccess" value="update options"/>
			</td>
		</tr>
		
		<?php wp_nonce_field('wechat_admin_option_update');?>
		</table>
	</form>
	<?php else: //custom menu setting?>
	<h2><?php _e('Custom Menu Setting');?></h2>
	<form action="<?php echo esc_url( add_query_arg( array( 'action' => 'customMenu' ), admin_url( 'admin.php' ).'?page='.WECHAT_OPTION ) ); ?>" method="post" id="wechat_custom_menu_id">
		<div class="custom_menu">
			<div class="custom_header">
				<div class="item_title"><?php _e('Custom Menu Manage'); ?></div>
				<div class="item_op"  id="addMenu"><?php _e('Add Main Menu');?></div>
			</div>
			<div class="custom_menu_body" id="custom_menu_body">
				<div class="item_line" id="line_0">
					<div class="item_name"><?php _e('Menu Name');?></div>
					<div class="item_attr"><?php _e('Menu Attribute');?></div>
					<div class="item_value"><?php _e('Menu Value');?></div>
				</div>
				<?php show_custom_menu();
				?>
			</div>
		
		</div>
		<div class="submit">
			<input class="button button-primary" type="submit" name="custommenusettings" value="update options"/>
		</div>
		<?php wp_nonce_field('wechat_custom_menu_conf'); ?>
	</form>

	
	
	<?php endif;?>
</div>
<?php
}
//json_menu='{"button":[{"type":"click/view","name":"","key/url":"xxxxx"}{"name":"xxxx","sub_button":[{}{}{}]}{}]}'
function load_mywechat_style(){
	wp_register_style('mywechat',plugins_url('css/mywechat.css',__FILE__));
	wp_enqueue_style('mywechat');
	wp_register_script('mywechat',plugins_url('js/mywechat.js',__FILE__));
	wp_enqueue_script('mywechat');
}
add_action('admin_enqueue_scripts','load_mywechat_style');
function wechat_conf_admin_page(){//add menu
	add_menu_page('wechat',__('wechat setting'),9,WECHAT_OPTION,'mywechat_admin','','4');
}
add_action('admin_menu','wechat_conf_admin_page');//add menu
function wechat_init(){
	//initialize the wechat configuration
	$wechat_access_url=get_option('wechat_access_url');
	if(!$wechat_access_url){
		$wechat_access_url=home_url().'/?'.dirname( plugin_basename(__FILE__) );
	}
	update_option('wechat_access_url',$wechat_access_url);
	//load language package
	load_plugin_textdomain(MY_LANG,false,dirname( plugin_basename( __FILE__ ) ).'/language/');
}
add_action('admin_init','wechat_init');
$token=get_option('wechat_token');
if($token!='' && isset($_GET[$token])){
	require_once('includes/interface.php');
}

function show_custom_menu(){
	$customMenus=get_option('customMenuItemJson');
	$custArr=json_decode($customMenus,true);

	$id=1;//start from 1 for the first line
	if(isset($custArr['button']) && is_array($custArr['button'])){
		foreach($custArr['button'] as $menus){
			if(isset($menus['sub_button'])){
				
				echo '<div class="item_line" id="line_'.$id.'">
						<div class="item_name" id="name_'.$id.'">
							<input type="text" name="item['.$id.'][name]" value="'.$menus['name'].'"/>
						</div>
						<div class="item_attr" id="attr_'.$id.'">
							<select id="menu_'.$id.'" name="item['.$id.'][type]" onchange="menu_sel('.$id.')">
								<option name="Menu" value="Menu" selected>Menu</option>
								<option name="Button" value="click">Button</option>
								<option name="View" value="view">View</option>
							</select>
						</div>
						<div class="item_menu_add" id="a_'.$id.'" onclick="add_item_menu('.$id.')">ADD</div>
						<div id="line_submenu_'.$id.'"';//header of the submenu
				$countSub=0;
				foreach($menus['sub_button'] as $submenu){
					echo '<div class="clear"></div>
						<div class="item_line_m" style="margin-left:20px;" id="line_menu_'.$id.'_'.$countSub.'">
							<div class="item_name" id="name_menu_'.$id.'_'.$countSub.'">
								<input type="text" name="item['.$id.'][submenu]['.$countSub.'][name]" value="'.$submenu['name'].'" >
							</div>
							<div class="item_attr" id="attr_menu_'.$id.'_'.$countSub.'">
								<select id="sec_menu_'.$id.'_'.$countSub.'" name="item['.$id.'][submenu]['.$countSub.'][type]">
									<option name="Button" value="click"';
									if($submenu['type']=='click'){echo 'selected';}
									echo '>Button</option>
									<option name="View" value="view"';
									if($submenu['type']=='view'){echo 'selected';}
									echo '>View</option>
								</select>
							</div>
							<div class="item_value" id="value_menu_'.$id.'_'.$countSub.'">
								<input type="text" name="item['.$id.'][submenu]['.$countSub.'][key]" value="';
								if($submenu['type']=='click'){echo $submenu['key'];}
								else{echo $submenu['url'];}
								echo '">
							</div>
							<div class="item_del" id="d_menu_'.$id.'_'.$countSub.'" onclick="del_item_menu("'.$id.'_'.$countSub.'")">DEL</div>
						</div><!--item_line_m-->';
					$countSub+=1;
				}
				echo '</div><!--#line submenu--></div><!--item line -->';
			}else{//ordinary menu
				echo '<div class="item_line" id="line_'.$id.'">
						<div class="item_name" id="name_'.$id.'">
							<input type="text" name="item['.$id.'][name]" value="'.$menus['name'].'"/>
						</div>
						<div class="item_attr" id="attr_'.$id.'">
							<select id="menu_'.$id.'" name="item['.$id.'][type]" onchange="menu_sel('.$id.')">
								<option name="Menu" value="Menu">Menu</option>
								<option name="Button" value="click"';
								if($menus['type']=='click'){echo 'selected';}
								echo '>Button</option>
								<option name="View" value="view" ';
								if($menus['type']=='view'){echo 'selected';}
								echo '>View</option>
							</select>
						</div>
						<div class="item_value" id="value_'.$id.'">
							<input type="text" name="item['.$id.'][key]" value="';
							if($menus['type']=='view'){echo $menus['url'];}
							else{echo $menus['key'];}
							echo '"/>
						</div>
						<div class="item_del" id="d_'.$id.'" onclick="del_item('.$id.')">DEL</div>
					</div>';
			}
			$id+=1;
		}//foreach
	}//if set button
}//
function post_custom_item($data){
	$customMenuAppid=get_option('custom_menu_appid');
	$customMenuAppSecret=get_option('custom_menu_appsecret');
	if(isset($customMenuAppid) && isset($customMenuAppSecret) && !empty($customMenuAppid) && !empty($customMenuAppSecret)){
		$getAccessTokenUrl='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$customMenuAppid.'&secret='.$customMenuAppSecret;
		$html=file_get_contents($getAccessTokenUrl);
		$html_str=json_decode($html);
		if(isset($html_str->{'errcode'})){//failure
			$errorcode=$html_str->{'errmsg'};
			echo $errorcode;
			// exit(0);
		}else{//success
			$accessToken=$html_str->{'access_token'};
			update_option('customMenuAccessToken',$accessToken);
			$postCustomMenuUrl='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$accessToken;
			$ch=curl_init();
			$timeout=5;
			curl_setopt($ch,CURLOPT_URL,$postCustomMenuUrl);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
			$file_contents=curl_exec($ch);
			curl_close($ch);
			$retCont=json_decode($file_contents);
			$err_code=$retCont->{'errcode'};
			if($err_code==0){
				echo '<div class="updated"><p>'.__('Success!You changes were successfully saved').'</p></div>';
			}else{
				echo '<div class="error"><p>'.__('Whoops...').'</p></div>';
			}
		}
	}
}//post_custom_item