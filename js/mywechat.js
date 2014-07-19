jQuery(document).ready(function(){
	jQuery("#addMenu").click(function(){
		var $itemContentLength=jQuery("#custom_menu_body .item_line").length;
		// alert($itemContent);
		if($itemContentLength<=3){
			var $max_nu=jQuery("#custom_menu_body .item_line").last().attr('id');
			// alert($max_nu);
			var $curId=parseInt($max_nu.substr(5))+1;
			
			// $curId=$curIdA[1]++;
			jQuery("#custom_menu_body").append("<div class='item_line' id='line_"+$curId+"'><div class='item_name' id='name_"+$curId+"'><input type='text' name='' id='name_"+$curId+"'/></div><div class='item_attr' id='attr_"+$curId+"'><select ><option name='Menu' value='Menu'>Menu</option><option name='Button' value='Button' selected >Button</option><option name='View' value='View'>View</option></select></div><div class='item_value'><input type='text'/></div><div class='item_del' id='d_"+$curId+"' onclick='del_item("+$curId+")'>DEL</div></div>");
			
		}else{
			alert('max length');
		}
	});
	jQuery("#custom_menu_body div.item_del").click(function(){
		jQuery(this).remove();
	});
});
function del_item(_id){
	var $lineId="line_"+_id;
	jQuery("div[id='"+$lineId+"']").remove();
}