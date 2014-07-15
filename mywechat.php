<?php 
/*
Plugin Name:mywechat
Plugin URI:http://tech.leepine.com
Author:Leez
Description: this is used to manage your wechat platform
Author URI:http://tech.leepine.com
*/

function mywechat_admin(){
	if(isset($_POST['wechataccess'])){
		if(wp_verify_nonce($_POST['_wpnonce'],'wechat_admin_option_update')){
			update_option('wechat_token',stripslashes($_POST['wechat_token']));
			update_option('wechat_access_url',stripslashes(home_url().'/?'.$_POST['wechat_token']));
			echo '<div class="updated"><p>'.__('Success!You changes were successfully saved').'</p></div>';
		}else{
			echo '<div class="error"><p>'.__('Whoops...').'</p></div>';
		}
	}
?>
<!--
admin setting about wechat
-->
<div class="wrap">
	<?php screen_icon();?>
	<h2>Wechat Configuration</h2>
	<form action="" method="post" id="wechat_conf_form">
		<table class="form-table">
		<tr class="form-field">
			<th><label for="Wechat_token">Token</label></th>
			<td><input type="text" name="wechat_token" id="wechat_token" value="<?php echo esc_attr(get_option('wechat_token'));?>"/></td>
		</tr>
		
		<tr class="form-field">
			<th><label for="Wechat_access_url">Url</label></th>
			<td><label><?php echo esc_attr(get_option('wechat_access_url'));?></label></td>
		</tr>
		
		<tr>
			<td>
				<input class="button button-primary" type="submit" name="wechataccess" value="update options"/>
			</td>
		</tr>
		
		<?php wp_nonce_field('wechat_admin_option_update');?>
		</table>
	</form>
</div>
<?php
}
function wechat_conf_admin_page(){//add menu
	add_menu_page('wechat','wechat setting',9,'wechat_options','mywechat_admin','','4');
}
add_action('admin_menu','wechat_conf_admin_page');//add menu
function wechat_init(){
	//initialize the wechat configuration
	$wechat_access_url=get_option('wechat_access_url');
	if(!$wechat_access_url){
		$wechat_access_url=home_url().'/?'.dirname(plugin_basename(__FILE__));
	}
	update_option('wechat_access_url',$wechat_access_url);
}
add_action('admin_init','wechat_init');
$token=get_option('wechat_token');
if($token!='' && isset($_GET[$token])){
	require_once('includes/interface.php');
}