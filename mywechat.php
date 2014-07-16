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
	<?php else: //custom menu setting?>
	<h2><?php _e('Custom Menu Setting');?></h2>
	<div id="menu-management-liquid">
		<div id="menu-management">
			<form id="custom-menu" action="" method="post" enctype="multipart/form-data">
				<div id="post-body">
					<div class="AApostbox post-body-content">
						<h3><?php _e( 'Menu Structure' ); ?></h3>
						<ul class="menu ui-sortable" id="menu-to-edit">
							<li id="menu-item" class="menu-item">
								nice
							</li>
						</ul>
					</div>
				</div><!--post body-->
			</form>
		</div>
	</div>

	<?php endif;?>
</div>
<?php
}
function load_mywechat_style(){
	wp_register_style('default',get_template_directory_uri().'/mywechat.css','','','');
}
add_action('init','load_mywechat_style');
function wechat_conf_admin_page(){//add menu
	add_menu_page('wechat',__('wechat setting'),9,WECHAT_OPTION,'mywechat_admin','','4');
}
add_action('admin_menu','wechat_conf_admin_page');//add menu
function wechat_init(){
	//initialize the wechat configuration
	$wechat_access_url=get_option('wechat_access_url');
	if(!$wechat_access_url){
		$wechat_access_url=home_url().'/?'.dirname(plugin_basename(__FILE__));
	}
	update_option('wechat_access_url',$wechat_access_url);
	//load language package
	load_plugin_textdomain(MY_LANG,false,dirname(plugin_basename(__FILE__).'/language/'));
}
add_action('admin_init','wechat_init');
$token=get_option('wechat_token');
if($token!='' && isset($_GET[$token])){
	require_once('includes/interface.php');
}