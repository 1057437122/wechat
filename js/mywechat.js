jQuery(document).ready(function(){
	jQuery("#addMenu").click(function(){
		var $itemContentLength=jQuery("#custom_menu_body .item_line").length;
		// alert($itemContent);
		if($itemContentLength<=3){
			var $max_nu=jQuery("#custom_menu_body .item_line").last().attr('id');
			// alert($max_nu);
			var $curId=parseInt($max_nu.substr(5))+1;
			
			// $curId=$curIdA[1]++;
			jQuery("#custom_menu_body").append("<div class='item_line' id='line_"+$curId+"'><div class='item_name' id='name_"+$curId+"'><input type='text'  name='item["+$curId+"][name]'/></div><div class='item_attr' id='attr_"+$curId+"'><select id='menu_"+$curId+"' name='item["+$curId+"][type]' onchange='menu_sel("+$curId+")'><option name='Menu' value='Menu'>Menu</option><option name='Button' value='click' selected >Button</option><option name='View' value='view'>View</option></select></div><div class='item_value' id='value_"+$curId+"'><input type='text' name='item["+$curId+"][key]'/></div><div class='item_del' id='d_"+$curId+"' onclick='del_item("+$curId+")'>DEL</div></div>");
			
		}else{
			alert('max length');
		}
	});

});
function del_item(_id){
	var $lineId="line_"+_id;
	jQuery("div[id='"+$lineId+"']").remove();
}
function menu_sel(_id){
	var $sel_id="#menu_"+_id;
	// alert(jQuery(this).val());
	// alert(jQuery($sel_id).val());
	var $curItem=jQuery($sel_id).val();
	var $valueItemLength=jQuery('#value_'+_id).length;
	if(($curItem=='click' || $curItem=='view') && $valueItemLength<=0){
		jQuery('#line_'+_id).append("<div class='item_value' id='value_"+_id+"'><input type='text' name='item["+_id+"][key]'/></div><div class='item_del' id='d_"+_id+"' onclick='del_item("+_id+")'>DEL</div>")
	}
	if($curItem=='Menu'){
		jQuery('#value_'+_id).remove();
		jQuery('#d_'+_id).remove();
	}
}