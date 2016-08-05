var row_index=-1;
$(document).ready(function(){
	$.extend($.fn.form.methods, {
        getData: function(jq, params){
            var formArray = jq.serializeArray();
            var oRet = {};
            for (var i in formArray) {
                if (typeof(oRet[formArray[i].name]) == 'undefined') {
                    if (params) {
                        oRet[formArray[i].name] = (formArray[i].value == "true" || formArray[i].value == "false") ? formArray[i].value == "true" : formArray[i].value;
                    }
                    else {
                        oRet[formArray[i].name] = formArray[i].value;
                    }
                }
                else {
                    if (params) {
                        oRet[formArray[i].name] = (formArray[i].value == "true" || formArray[i].value == "false") ? formArray[i].value == "true" : formArray[i].value;
                    }
                    else {
                        oRet[formArray[i].name] += "," + formArray[i].value;
                    }
                }
            }
            return oRet;
        }
    });

    $('#dg').datagrid({
    	onSelect:function(rowIndex,rowData){
        	if(rowData){
        		row_index=rowIndex;
            }
        },
        onLoadSuccess:function(data){
        	$('#dg').datagrid('selectRow',row_index);
        }
    });

	$.fn.extend({
	    combox_fill:function(list,val_key,txt_key,sel_val){
	    	var options='';
	    	for(var x in list){
	    		options+='<option value="'+list[x][val_key]+'">'+list[x][txt_key]+'</option>';
	    	}
	    	$(this).html(options);
	    	$(this).val(sel_val);
	     },
 		combox_cascade:function(data,sel_id){
    		var contain	=$(this).empty();
    		var input	=$('<input type="hidden" name="'+(contain.attr('name'))+'" value="'+sel_id+'" />');
    		var select	=null;
    		contain.append(input);

    		var pid=0;
    		do{
    			pid=0;
        		for(var x in data){
            		if(data[x][1]==sel_id){
            			pid=data[x][0];
            			break;
                	}
            	}
        		select=$('<select></select>');
        		contain.prepend(select);

        		select.html('<option value="-1">--选择--</option>');
        		for(var x in data){
            		if(data[x][0]==pid){
            			select.append('<option value="'+data[x][1]+'">'+data[x][2]+'</option>');
                	}
            	}

        		select.bind('change',onchange);
        		select.val(sel_id);
        		sel_id=pid;
        	}while(pid!=0);

        	function onchange(){
            	var $this=$(this);
        		var children=contain.children('select');
        		var index=children.index($this);
        		var count=children.length;

        		var sel_val=$this.val();
        		var sub_data=[];
        		if(sel_val!=-1){
            		for(var x in data){
                		if(sel_val==data[x][0]){
                			sub_data.push(data[x]);
                    	}
                	}
            	}
				if(sub_data.length<=0){
            		for(var i=index+1;i<count;i++){
                		if(children.get(i)){
                			children.get(i).remove();
                    	}
                	}
            		input.val(sel_val);
				}else{
            		for(var i=index+2;i<count;i++){
                		if(children.get(i)){
                			children.get(i).remove();
                    	}
                	}
                	var sub_select=null;
                	if(children.get(index+1)){
                		sub_select=$(children.get(index+1));
                    }else{
                    	sub_select=$('<select></select>').appendTo(contain);
                    }
                	sub_select.html('<option value="-1">--选择--</option>');
                	for(var x in sub_data){
                		sub_select.append('<option value="'+sub_data[x][1]+'">'+sub_data[x][2]+'</option>');
	                }
                	sub_select.unbind('change').bind('change',onchange);
				}
            }
    	}
	});
});

function locate_row_index_by_idval(fieldname,fieldvalue){
	var data=$('#dg').datagrid('getData');
	for(var x in data){
    	if(data[x][fieldname]==fieldvalue){
    		row_index=x;
    		break;
        }
    }
}
function str_repeat($char,$len){
	return (new Array($len+1).join($char))
}
function clone(target) {
    var buf;
    if (target instanceof Array) {
        buf = [];  //创建一个空的数组
        var i = target.length;
        while (i--) {
            buf[i] = clone(target[i]);
        }
        return buf;
    }else if (target instanceof Object){
        buf = {};  //创建一个空对象
        for (var k in target) {  //为这个对象添加新的属性
            buf[k] = clone(target[k]);
        }
        return buf;
    }else{
        return target;
    }
}

function getStrLength(str) {
    var cArr = str.match(/[^\x00-\xff]/ig);
    return str.length + (cArr == null ? 0 : cArr.length);
}