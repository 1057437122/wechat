jQuery(document).ready(function(){
	jQuery("#addMenu").click(function(){
		var $itemContent=jQuery("#custom_menu_body .item_line").length;
		// alert($itemContent);
		if($itemContent<=3){
			jQuery("#custom_menu_body").append("<div class='clear'></div><div class='item_line' id='No["+$itemContent+"]'><div class='item_name'><input type='text' name=''/></div><div class='item_attr'><select><option name='Menu' value='Menu'>Menu</option><option name='Button' value='Button' selected >Button</option><option name='View' value='View'>View</option></select></div><div class='item_value'><input type='text'/></div><div class='item_del' id='d["+$itemContent+"]'>DEL</div></div>");
		}else{
			alert('max length');
		}
	});
	jQuery(".item_del").click(function(){
		var $curId=jQuery(this).index(); 
		jQuery("#custom_menu_body").remove("div[id=No["+$curId+"]]");
	});
});