<?php 
/*
Plugin Name:mywechat
Plugin URI:http://tech.leepine.com
Author:Leez
Description: this is used to manage your wechat platform
Author URI:http://tech.leepine.com
*/
/*

		Array
(
    [1] => Array
        (
            [name] => F1
            [type] => click
            [key] => 11111
        )

    [2] => Array
        (
            [name] => F2
            [type] => Menu
            [submenu] => Array
                (
                    [1] => Array
                        (
                            [name] => F21
                            [type] => click
                            [key] => 21212
                        )

                    [2] => Array
                        (
                            [name] => F22
                            [type] => click
                            [key] => 22222
                        )

                    [3] => Array
                        (
                            [name] => F23
                            [type] => click
                            [key] => 2323
                        )

                )

        )

    [3] => Array
        (
            [name] => F3
            [type] => click
            [key] => 222223333
        )

)
*/
define(WECHAT_OPTION,'wechat_options');
define(MY_LANG,'leez');
function mywechat_admin(){
	if(!isset($_GET['action']) && isset($_POST['wechataccess'])){
		if(wp_verify_nonce($_POST['_wpnonce'],'wechat_admin_option_update')){
			update_option('wechat_token',stripslashes($_POST['wechat_token']));
			update_option('wechat_access_url',stripslashes(home_url().'/?'.$_POST['wechat_token']));
			if(isset($_POST['enable_custom_menu']) && $_POST['enable_custom_menu']=='enable_custom_menu'){
				$customMenuAcc=1;
				if(isset($_POST['custom_menu_appid']) && !empty($_POST['custom_menu_appid']) && isset($_POST['custom_menu_appsecret']) && !empty($_POST['custom_menu_appsecret'])){
					update_option('custom_menu_appid',stripslashes($_POST['custom_menu_appid']));
					update_option('custom_menu_appsecret',stripslashes($_POST['custom_menu_appsecret']));
				}
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
					$tmp='{"type":"'.$items['type'].',"name":"'.$items['name'].',"key":"'.$items['key'].'"},';
				}
			}
			print_r($_POST['item']);
			echo '<br>';
			var_dump($_POST['item']);
			echo '<br>';
			echo json_encode($_POST['item']);
			echo '<br>';
			var_dump(json_encode($_POST['item']));
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
		
		<tr>
			<th><input type="checkbox" name="enable_custom_menu" value="enable_custom_menu" <?php if($customMenuAcc){echo 'checked';}?>><?php _e('Enable Custom Menu');?></th>
		</tr>
		
		<?php if(isset($customMenuAcc)): ?>
		
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