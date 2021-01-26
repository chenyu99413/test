$.fn.tooltip.defaults.position = 'top';

/**
 * 回车转Tab键
 * @param obj
 * @param e
 * @returns {Boolean}
 */
function enter2tab(obj,e){
	e.preventDefault();
	var self = $(obj), form = self.parents('form:eq(0)'), focusable, next;
	focusable = form.find('input,select,button').filter(':visible[tabindex!=-1]').filter(':not([readonly])');
	next = focusable.eq(focusable.index(obj)+1);
	if (next.length && $(obj).is('[type!=submit]')) {
	    next.focus().select();
	} else {
		form.find('[type=submit]').trigger('click');
	}
	return false;
}
/**
 * 页面提交验证
 * 
 * @param method
 * @param flag
 * @returns {Boolean}
 */
$(function() {
	$("form").submit(function() {
		if (!$(this).form('validate')) {
			$message = "";
			$("form").find(".validatebox-invalid").each(function() {
				$message += "【" + $(this).parents("td:last").prev().text() + "】";
			});
			$.messager.alert('Error', '请检查 ' + $message + ' 是否正确。');
			return false;
		}
		//combogrid mustSelect check
		if ($(this).find('.combogrid-f[mustSelected]')){
			$message='';
			$(this).find('.combogrid-f[mustSelected]').each(function(){
				if ( $(this).combogrid('grid').datagrid('getSelected') ==null){
					$message += "【" + $(this).parents("td:last").prev().text() + "】";
				}
			})
			if ($message.length){
				$.messager.alert('Error', '请检查 ' + $message + ' 是否正确');
				return false;
			}
		}
	});
	
	$("th.sortable").each(function() {
		$(this).bind("click", function() {
			var form = $("form");
			var clazz = $(this).attr("class");
			var sort = $(this).attr("sort");
			if (clazz == "sortable") {
				form.append("<input type='hidden' name='sort' value='" + sort + " asc' />");
			} else if (clazz.indexOf("desc") > 0) {
				form.append("<input type='hidden' name='sort' value='" + sort + " asc' />");
			} else {
				form.append("<input type='hidden' name='sort' value='" + sort + " desc' />");
			}
			form.submit();
		});
	});
	$('select[readonly]').each(function(){
		$(this).find('option:not(:selected)').attr('disabled','disabled');
	})
});

/**
 * 全选
 * 
 * @param obj
 * @param name
 */
function SelectAll(obj, name) {
	$("." + name + ':visible').each(function(index, value) {
		$(value).attr("checked", obj.checked);
	});
}

/**
 * 检查特殊字符串
 * 
 * @param obj
 * @returns {Boolean}
 */
function CheckSpecialCharacters(obj) {
	var reg = /^[^*$#%@&()\[\]\\\/|<>.\;:"]+$/;
	if (obj.value == "" || reg.test(obj.value))
		return true;
	else
		return false;
}

/**
 * 格式化数量
 * 
 * @param obj
 */
function FormatInt(obj) {
	var val = $.trim(obj.value);
	if (val == "") {
		obj.value = "";
		return;
	}
	if (!isNaN(val) && parseInt(val) >= 0) {
		obj.value = val;
	} else {
		obj.value = "1";
	}
}

/**
 * 格式化小数
 * 
 * @param obj
 * @param length
 */
function FormatDecimal(obj, length) {
	var len = 2;
	if (length != null && !isNaN(length) && parseInt(length) > 0)
		len = length;
	var val = $.trim(obj.value);
	if (val == "") {
		var zero = "";
		for ( var i = 0; i < len; i++) {
			zero += "0";
		}
		obj.value = "0." + zero;
		return;
	}
	if (!isNaN(val) && parseFloat(val) >= 0) {
		obj.value = ToDecimal(val, len);
	} else {
		var zero = "";
		for ( var i = 0; i < len; i++) {
			zero += "0";
		}
		obj.value = "0." + zero;
	}
}

/**
 * 格式化数字
 * 
 * @param obj
 * @param length
 */
function ToDecimal(number, length) {
	var float = parseFloat(number);
	if (isNaN(float)) {
		return false;
	}
	var float = Math.round(number * 100) / 100;
	var result = float.toString();
	var temp = result.indexOf('.');
	if (temp < 0) {
		temp = result.length;
		result += '.';
	}
	while (result.length <= temp + length) {
		result += '0';
	}
	return result;
}

/**
 * 判断数量
 * 
 * @param number
 * @returns {Boolean}
 */
function CheckNumber(number) {
	if (number == "")
		return false;
	else
		return !isNaN(number);
}

/**
 * 检查英文
 * 
 * @param obj
 */
function CheckEnglish(obj) {
	return obj.value.match(/[\x01-\xFF]*/);
}

/**
 * 格式化金额
 * 
 * @param number
 * @param precision
 * @returns {String}
 */
function FormatMoney(number, precision) {
	var result = "";
	try {
		if (number == null || number == "" || isNaN(number)) {
			return "0";
		}

		precision = precision > 0 && precision <= 20 ? precision : 2;
		number = parseFloat((number + "").replace(/[^\d\.-]/g, "")).toFixed(precision) + "";
		var int = number.split(".")[0].split("").reverse();
		var dec = number.split(".")[1];
		for ( var i = 0; i < int.length; i++) {
			result += int[i] + ((i + 1) % 3 == 0 && (i + 1) != int.length ? "," : "");
		}

		result = result.split("").reverse().join("") + "." + dec;
		if (result.indexOf(",") == 0) {
			result = result.substring(1, result.length);
		} else if (result.indexOf("-,") == 0) {
			result = "-" + result.substring(2, result.length);
		}
		return result;
	} catch (e) {
		return "0";
	}
}

/**
 * 日期格式化
 * 
 * @param d
 * @param format
 * @returns
 */
function FormatDate(d, format) {
	var year = d.getFullYear();
	var month = d.getMonth() + 1;
	var date = d.getDate();
	var hours = d.getHours();
	var minutes = d.getMinutes();
	var seconds = d.getSeconds();
	return format.replace("yyyy", year).replace("MM", month > 9 ? month : "0" + month).replace("dd", date > 9 ? date : "0" + date).replace("hh", hours > 9 ? hours : "0" + hours).replace("mm", seconds > 9 ? seconds : "0" + seconds).replace("ss", minutes > 9 ? minutes : "0" + minutes);
}

/**
 * JSON日期格式转换
 * 
 * @param obj
 * @param format
 * @returns
 */
function JsonDateFormat(obj, format) {
	try {
		var year = (1900 + obj.year).toString();
		var month = (obj.month + 1).toString();
		var date = obj.date.toString();
		var hours = obj.hours.toString();
		var minutes = obj.minutes.toString();
		var seconds = obj.seconds.toString();
		return format.replace("yyyy", year).replace("MM", month.length > 1 ? month : "0" + month).replace("dd", date.length > 1 ? date : "0" + date).replace("hh", hours.length > 1 ? hours : "0" + hours).replace("mm", seconds.length > 1 ? seconds : "0" + seconds).replace("ss",
				minutes.length > 1 ? minutes : "0" + minutes);
	} catch (exp) {
		return "";
	}
}

/**
 * 日期字符串转JSON日期
 * 
 * @param date
 * @returns
 */
function DateToJSON(date) {
	var result = "";

	try {
		var reg = /^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$/;
		if (!reg.test(date)) {
			return "null";
		}
		var d = $.trim(date).replace("/-/g", "");
		var year = parseInt(d.substring(0, 4)) - 1900;
		var month = parseInt(d.substring(5, 7)) - 1;
		var day = parseInt(d.substring(8, 10));
		var hours = 0;
		var minutes = 0;
		var seconds = 0;
		result = '{"year":' + year + ',"month":' + month + ',"date":' + day;
		if (d.length > 10) {
			hours = parseInt(d.substring(11, 13));
			minutes = parseInt(d.substring(14, 16));
			seconds = parseInt(d.substring(17, 19));
		}
		result += ',"hours":' + hours + ',"minutes":' + minutes + ',"seconds":' + seconds + '}';
		return result;
	} catch (e) {
		return "null";
	}
}

/**
 * 文件上传
 * 
 * @param 文件对象
 * @param 上传地址
 */
function UploadFile(obj, url) {
	// 定义一个form表单
	var form = $("<form>");
	form.attr("style", "display:none");
	form.attr("target", "");
	form.attr("method", "post");
	form.attr("enctype", "multipart/form-data");
	form.attr("action", url);
	$("body").append(form);
	form.append(obj);
	form.submit();
}

/**
 * 导出
 */
function ExportTable(url, table_name, fileName) {
	var json = "";
	if (table_name != null) {
		$("#" + table_name).find("tr").each(function() {
			var tr = "";
			var items = null;
			if ($(this).find("td").length > 0) {
				items = $(this).find("td");
			} else if ($(this).find("th").length > 0) {
				items = $(this).find("th");
			}
			items.each(function(index, element) {
				var value = "";
				if ($(this).find("a").length == 1) {
					value = $.trim($(items).find("a").eq(0).text());
				} else {
					value = $.trim($(this).clone().children().remove().end().text());
				}
				if(isNaN(value) || value.length>11){
					value = "'" + value;
				}
				tr += '"' + value + '",';
			});
			tr = "[" + tr.substring(0, tr.length - 1) + "],";
			json += tr;
		});
		json = "[" + json.substring(0, json.length - 1) + "]";
	}

	// 定义一个form表单
	var form = $("<form>");
	form.attr("style", "display:none");
	form.attr("target", "");
	form.attr("method", "post");
	form.attr("action", url);
	var hidden_json = $("<input>");
	hidden_json.attr("type", "hidden");
	hidden_json.attr("name", "json");
	hidden_json.attr("value", json);
	var hidden_fileName = $("<input>");
	hidden_fileName.attr("type", "hidden");
	hidden_fileName.attr("name", "fileName");
	hidden_fileName.attr("value", fileName);
	$("body").append(form);
	form.append(hidden_json);
	form.append(hidden_fileName);
	form.submit();
}

var tr = null;

/**
 * 新增行
 */
function NewRow(list, obj) {
	if (list == undefined || list == null || obj == undefined || obj == null)
		return false;
	var type = "<a href='javascript:void(0)' class='btn btn-mini btn-primary' onclick='AddRow(" + JSON.stringify(list) + ");' style='margin-left:10px;margin-top:1px;'><i class='icon-save'></i>保存</a>";
	ViewRow(list, obj, type);
}

/**
 * 编辑行
 */
function EditRow(list, obj) {
	if (list == undefined || list == null || obj == undefined || obj == null)
		return false;
	var type = "<a href='javascript:void(0)' class='btn btn-mini btn-primary' onclick='SaveRow(" + JSON.stringify(list) + ");' style='margin-left:10px;margin-top:1px;'><i class='icon-save'></i>保存</a>";
	ViewRow(list, obj, type);
}

/**
 * 显示行
 */
function ViewRow(list, obj, type) {
	tr = $(obj).parent().parent();
	var html = "<div id='mask'><div style='left:" + tr.offset().left + "px;top:" + tr.offset().top + "px;width:" + tr.width() + "px;height:" + (tr.height() - 1) + "px;z-index:9002;position:absolute;'><table style='width:100%;background-color:#FFE48D;'><tr style='height:" + tr.height() + "px'>";
	$(tr).children().each(
			function(index) {
				if (index < $(tr).children().length - 1) {
					html += "<td style='width:" + ($(this).width() + 19) + "px'>";
					if (list[index] == undefined) {
						return false;
					}
					if (list[index].type == "text") {
						var value = $.trim($(this).text());
						if (value == "" && list[index].value != undefined) {
							value = list[index].value;
						}
						html += "<input id='input_" + index + "' class='easyui-textbox' " + (list[index].required == null ? "" : "required='" + list[index].required + "' ") + (list[index].validType == null ? "" : "validType=\"" + list[index].validType + "['" + list[index].checkUrl + "']" + "\" ")
								+ "style='width:95%;' value='" + value.replace(/(['])/g,'&#39') + "'></input>";
					} else if (list[index].type == "select") {
						html += "<select id='input_" + index + "' style='margin-top:3px;width:95%;' value='" + $.trim($(this).text()) + "'>";
						for ( var i in list[index].option) {
							html += "<option value='" + list[index].option[i].id + "'" + ($.trim($(this).text()) == list[index].option[i].text ? " selected " : "") + " >" + list[index].option[i].text + "</option>";
						}
						html += "</select>";
					} else if (list[index].type == "number") {
						var value = $.trim($(this).text());
						if (value == "" && list[index].value != undefined) {
							value = list[index].value;
						}
						html += "<input id='input_" + index + "' class='easyui-numberbox' " + (list[index].required == null ? "" : "required='" + list[index].required + "' ") + (list[index].precision == null ? "" : "precision='" + list[index].precision + "' ")
								+ (list[index].min == null ? "" : "min='" + list[index].min + "' ") + (list[index].max == null ? "" : "max='" + list[index].max + "' ") + (list[index].validType == null ? "" : "validType=\"" + list[index].validType + "\" ") + "style='width:95%;' value='" + value
								+ "'></input>";
					} else if (list[index].type == "date") {
						var value = $.trim($(this).text());
						if (value == "" && list[index].value != undefined) {
							value = list[index].value;
						}
						html += "<input id='input_" + index + "' class='easyui-datebox' validType='dateFormate' " + (list[index].required == null ? " " : "required='" + list[index].required + "' ") + "style='width:100px' value='" + value + "'></input>";
					} else if (list[index].type == "checkbox") {
						html += "<input id='input_" + index + "' type='checkbox' " + (list[index].required == null ? "" : "required='" + list[index].required + "' ") + "style='margin-top:-1px;width:12px;' " + ($.trim($(this).html()) == "" ? "" : "checked='checked'")
								+ "></input>";
					} else if (list[index].type == "combogrid" && list[index].option == "country") {
						var value = $(this).children().eq(0).val();
						if (value == "" && list[index].value != undefined) {
							value = list[index].value;
						} else if (value == undefined) {
							value = "";
						}
						html += "<input id='input_" + index + "' value='" + value + "' class='easyui-combogrid' " + " data-options=\"panelWidth: 500,mode:'remote',idField:'code_word_two',textField:'code_word_two',fitColumns:true,url:'" + list[index].url
								+ "',columns:[[{field:'code_word_two',title:'二字码',width:50,align:'center'},{field:'code_word_three',title:'三字码',width:70,align:'center'},{field:'chinese_name',title:'中文名称',width:150},{field:'english_name',title:'英文名称',width:230}]]\" "
								+ (list[index].required == null ? " " : "required='" + list[index].required + "' ") + " validType=\"checkExist['" + list[index].checkUrl + "']\" style='margin-top:-1px;width:95%;' />";
					}else if(list[index].type == "combogrid" && list[index].option == "customs"){
						var value = $(this).children().eq(0).val();
						if (value == "" && list[index].value != undefined) {
							value = list[index].value;
						} else if (value == undefined) {
							value = "";
						}
						if(list[index].value!='' && list[index].value != undefined){
							value =list[index].value;
						}
						html += "<input id='input_" + index + "' class='easyui-combogrid' " + " data-options=\"panelWidth: 500, value:'" + value +"',mode:'remote',onHidePanel:customsChange,selectOnNavigation:false,idField:'customs_id',textField:'customs_abbreviation',fitColumns:true,url:'" + list[index].url
								+ "',columns:[[{field:'customs_code',title:'客户代码',width:150,align:'center'},{field:'customs_abbreviation',title:'客户简称',width:200,align:'center'}]]\" ,"
								+ (list[index].required == null ? " " : "required='" + list[index].required + "' ,fitColumns: true") + " type='text' style='margin-top:-1px;width:95%;' />";
					} else {
						html += $.trim($(this).text());
					}
					html += "</td>";
				} else {
					html += "<td><div style='margin-top:-12px;left:" + ($(this).offset().left - tr.offset().left) + "px;width:" + ($(this).width() * 2 + 18) + "px;height:" + $(this).height() + "px;border-radius:0px;position:absolute;'>" + type
							+ "<a href='javascript:void(0)' class='btn btn-mini btn-inverse' onclick='CloseRow();' style=' margin-left:3px;margin-top:1px;'><i class='icon-remove'></i>关闭</a></div></td>";
				}
			});
	html += "</tr></table></div><div class='window-mask' style='width:" + document.body.scrollWidth + "px; height:" + document.body.scrollHeight + "px;display:block; z-index:9000;'></div></div>";
	$("body").append(html);
	$.parser.parse($("#mask"));

	// 回调
	if (typeof (CallBefore) == "function") {
		CallBefore($(tr));
	}
}

/**
 * 保存行
 */
function SaveRow(list) {
	// 回调
	if (typeof (SaveBefore) == "function") {
		if(!SaveBefore(list, $(tr).parent().parent().attr("id"))){
			return false;
		}
	}
	var validate = true;
	var check_ratingdate=true;
	// 检查必填项
	$(tr).children().each(function(index) {
		if (index < $(tr).children().length - 1) {
			if (list[index] == undefined) {
				return false;
			}
			if (list[index].type == "text") {
				if (!$("#input_" + index).textbox("isValid")) {
					validate = false;
					return false;
				}
			} else if (list[index].type == "number") {
				if (!$("#input_" + index).numberbox("isValid")) {
					validate = false;
					return false;
				}
			} else if (list[index].type == "date") {
				if (!$("#input_" + index).datebox("isValid")) {
					validate = false;
					return false;
				}else if(typeof($("#closeBalanceDay").val()) !="undefined" && $("#closeBalanceDay").val()!='' && $("#input_" + index).datebox("getValue") !=''){
					 var now = new Date();
					 var day=now.getDate();
					 var year = now.getFullYear();
					 //登账日期时间戳
					 var rating_date = new Date($("#input_" + index).datebox("getValue").replace(/\-/gi,"/")).getTime();
					 //未到关账日，登帐日可以输入上月1日之后的任意日期
					 if(day <= $("#closeBalanceDay").val()){
						 var month = now.getMonth();
					     if(month==0){
					        month=12;
					        year=year-1;
					     }
					     if (month < 10) {
					        month = "0" + month;
					     }
					     //上个月的第一天
						 var pre= year + "-" + month + "-" + "01";
						 var pre_fist=new Date(pre.replace(/\-/gi,"/")).getTime();
						 if(rating_date<pre_fist){
							alert("登账日期不能选择 "+pre+" 之前的日期"); 
							validate = false;
							check_ratingdate=false;
							return false;
						 }
					 }else{
						 //本月第一天
						 var month = now.getMonth()+1;
						 if (month < 10) {
					        month = "0" + month;
					     }
						 var firstdate = year + '-' + month + '-01';
						 var first=new Date(firstdate.replace(/\-/gi,"/")).getTime();
						 if(rating_date<first){
							 alert("登账日期不能选择 "+firstdate+" 之前的日期");
							 validate = false;
							 check_ratingdate=false;
							 return false; 
						 }
					 }
				}
			} else if (list[index].type == "select" && list[index].required == "true") {
				if ($("#input_" + index).find("option:selected").text() == "") {
					validate = false;
					return false;
				}
			} else if (list[index].type == "combogrid" && list[index].required == "true") {
				if (!$("#input_" + index).combogrid("isValid")) {
					validate = false;
					return false;
				}
			}
		}
	});
	if (validate) {
		$(tr).children().each(function(index) {
			if (index < $(tr).children().length - 1) {
				if (list[index] == undefined) {
					return false;
				}
				var value = "";
				if (list[index].type == "text") {
					value = $("#input_" + index).textbox("getValue");
				} else if (list[index].type == "select") {
					value = $("#input_" + index).find("option:selected").text();
					value += "<input type='hidden' value='" + ($("#input_" + index).val() == null ? "" : $("#input_" + index).val()) + "' />";
					$(tr).children().eq(index).html(value);
					return true;
				} else if (list[index].type == "number") {
					value = $("#input_" + index).numberbox("getValue");
				} else if (list[index].type == "date") {
					value = $("#input_" + index).datebox("getValue");
				} else if (list[index].type == "checkbox") {
					value = $("#input_" + index).attr("checked") == "checked" ? "<i class='icon-ok'></i>" : "";
					$(tr).children().eq(index).html(value);
					return true;
				} else if (list[index].type == "combogrid") {
					value = $("#input_" + index).combogrid("getText");
					value += "<input type='hidden' value='" + $("#input_" + index).combogrid("getValue") + "' />";
					$(tr).children().eq(index).html(value);
					return true;
				} else {
					return true;
				}
				$(tr).children().eq(index).text(value);
			}
		});
		CloseRow();

		// 回调
		if (typeof (CallBack) == "function") {
			CallBack($(tr), $(tr).parent().parent().attr("id"));
		}
	} else {
		if(check_ratingdate)
		alert("请检查您的填写内容是否正确");
	}
}

/**
 * 添加行
 */
function AddRow(list) {
	// 回调
	if (typeof (SaveBefore) == "function") {
		if(!SaveBefore(list, $(tr).parent().parent().attr("id"))){
			return false;
		}
	}
	var validate = true;
	var check_ratingdate=true;
	// 检查必填项
	$(tr).children().each(function(index) {
		if (index < $(tr).children().length - 1) {
			if (list[index] == undefined) {
				return false;
			}
			if (list[index].type == "text") {
				if (!$("#input_" + index).textbox("isValid")) {
					validate = false;
					return false;
				}
			} else if (list[index].type == "number") {
				if (!$("#input_" + index).numberbox("isValid")) {
					validate = false;
					return false;
				}
			} else if (list[index].type == "date") {
				if (!$("#input_" + index).datebox("isValid")) {
					validate = false;
					return false;
				}else if(typeof($("#closeBalanceDay").val()) !="undefined" && $("#closeBalanceDay").val()!='' && $("#input_" + index).datebox("getValue") !=''){
					var now = new Date();
					 var day=now.getDate();
					 var year = now.getFullYear();
					 //登账日期时间戳
					 var rating_date = new Date($("#input_" + index).datebox("getValue").replace(/\-/gi,"/")).getTime();
					 //未到关账日，登帐日可以输入上月1日之后的任意日期
					 if(day <= $("#closeBalanceDay").val()){
						 var month = now.getMonth();
					     if(month==0){
					        month=12;
					        year=year-1;
					     }
					     if (month < 10) {
					        month = "0" + month;
					     }
					     //上个月的第一天
						 var pre= year + "-" + month + "-" + "01";
						 var pre_fist=new Date(pre.replace(/\-/gi,"/")).getTime();
						 if(rating_date<pre_fist){
							alert("登账日期不能选择 "+pre+" 之前的日期"); 
							validate = false;
							check_ratingdate=false;
							return false;
						 }
					 }else{
						 //本月第一天
						 var month = now.getMonth()+1;
						 if (month < 10) {
					        month = "0" + month;
					     }
						 var firstdate = year + '-' + month + '-01';
						 var first=new Date(firstdate.replace(/\-/gi,"/")).getTime();
						 if(rating_date<first){
							 alert("登账日期不能选择 "+firstdate+" 之前的日期");
							 validate = false;
							 check_ratingdate=false;
							 return false; 
						 }
					 }
				}
			} else if (list[index].type == "select" && list[index].required == "true") {
				if ($("#input_" + index).find("option:selected").text() == "") {
					validate = false;
					return false;
				}
			} else if (list[index].type == "combogrid" && list[index].required == "true") {
				if (!$("#input_" + index).combogrid("isValid")) {
					validate = false;
					return false;
				}
			}
		}
	});
	if (validate) {
		var html = "<tr>";
		$(tr).children().each(
				function(index) {
					if (index < $(tr).children().length - 1) {
						if (list[index] == undefined) {
							return false;
						}
						var value = "";
						if (list[index].type == "text") {
							value = $("#input_" + index).textbox("getValue");
						} else if (list[index].type == "select") {
							value = $("#input_" + index).find("option:selected").text();
							value += "<input type='hidden' value='" + ($("#input_" + index).val() == null ? "" : $("#input_" + index).val()) + "' />";
						} else if (list[index].type == "number") {
							value = $("#input_" + index).numberbox("getValue");
						} else if (list[index].type == "date") {
							value = $("#input_" + index).datebox("getValue");
						} else if (list[index].type == "checkbox") {
							value = $("#input_" + index).attr("checked") == "checked" ? "<i class='icon-ok'></i>" : "";
						} else if (list[index].type == "combogrid") {
							value = $("#input_" + index).combogrid("getText");
							value += "<input type='hidden' value='" + $("#input_" + index).combogrid("getValue") + "' />";
							list[index].value ='';
						} else {
							value = "";
						}
						html += "<td>" + value + "</td>";
					} else {
						html += "<td><a href='javascript:void(0)' class='btn btn-mini btn-info' onclick='EditRow(" + JSON.stringify(list)
								+ ",this);'><i class='icon-pencil'></i> 编辑</a><a href='javascript:void(0)' class='btn btn-mini btn-danger' onclick='DeleteRow(this);' style='margin-left:3px;'><i class='icon-trash'></i> 删除</a></td>";
					}
				});
		html += "</tr>";
		$(tr).before(html);
		CloseRow();

		// 回调
		if (typeof (CallBack) == "function") {
			CallBack($(tr).prev(), $(tr).prev().parent().parent().attr("id"));
		}
	} else {
		if(check_ratingdate)
		alert("请检查您的填写内容是否正确");
	}
}

/**
 * 关闭行
 */
function CloseRow() {
	$("#mask").remove();
}

/**
 * 删除
 */
function DeleteRow(obj) {
	if (confirm('确认要删除吗？')) {
		var tr = $(obj).parent().parent();
		var name = $(tr).parent().parent().attr("id");
		if (typeof (DeleteBefore) == "function") {
			DeleteBefore(tr);
		}
		$(tr).remove();
		if (typeof (CallBack) == "function") {
			CallBack(null, name);
		}
		return true;
	} else {
		return false;
	}
}

/**
 * combogrid 增强，自动筛选结果
 */
(function($) {
	$.fn.combogrid.defaults.delay=100;
	var oldQuery = $.fn.combogrid.defaults.keyHandler.query;
	$.fn.combogrid.defaults.keyHandler.query = function(q, event) {
		oldQuery.call(this, q);
		var opts = $(this).combogrid('options');
		if (opts.mode == 'local') {
			var g = $(this).combogrid('grid');
			// 保存原始列表
			var data;
			if ($(this).data('oldData') == undefined) {
				data = g.datagrid('getRows');
				$(this).data('oldData', data);
			} else {
				data = $(this).data('oldData');
			}
			// 模糊查询
			var newData = [];
			for ( var j = 0; j < data.length; j++) {
				row = data[j];
				right = false;
				for (i in row) {
					if (typeof (row[i]) == 'string' || typeof (row[i]) == 'number') {
						if (row[i].toString().toUpperCase().indexOf(q.trim().toUpperCase()) == 0) {
							right = true;
						}
					}
				}
				if (right) {
					newData.push(row);
				}
			}
			g.datagrid('loadData', newData);
			// 如果只有一个结果，默认选中
			if (newData.length == 1 && event.keyCode != 27 && event.keyCode != 8 && event.keyCode != 46) {
				$(this).combogrid('grid').datagrid('highlightRow',0);
			}else {
				// 如果在结果列里面有完全匹配的项，选中
				for ( var i = 0; i < newData.length; i++) {
					if (newData[i][opts.idField].toLowerCase() == q.trim().toLowerCase()) {
						$(this).combogrid('setValue', newData[i][opts.idField]);
						return;
					}
				}
			}
		}
	};
	//修正combogrid 默认载入地址不正确的问题
	$.fn.combogrid.defaults.onBeforeLoad=function(p){
		opt=$(this).datagrid('options');
		if (opt.mode=='remote'&&( p.q == undefined || p.q.length==0)){
			p.q=opt.value;
		}
	}
//	var $oldLoader=$.fn.combogrid.defaults.loader;
//	$.fn.combogrid.defaults.loader=function (a,b,c){
//		$oldLoader.call(a,b,c);
//	}
})(jQuery);

/**
 * 从二维数组中获得指定行的某个列值
 * 
 * @param data
 * @param matchCol
 * @param matchValue
 * @param showColname
 * @returns
 */
function getRowCol(data, matchCol, matchValue, showCol) {
	for (i in data) {
		if (data[i][matchCol] == matchValue) {
			return data[i][showCol];
		}
	}
}

/**
 * 重新加载下拉框
 * 
 * @param obj
 *            控件对象
 * @param data
 *            下拉框数据源
 * @param key
 *            下拉框值
 * @param value
 *            下拉框内容
 * @param def
 *            默认值
 * @param def_flag
 *            是否添加默认值
 */
function ReloadDropdownBox(obj, data, option) {
	// 是否有默认值标记
	var flag = false;

	// 默认值
	var def = option.value == undefined || option.value == null ? "" : option.value;

	// 清空下拉框
	$(obj).empty();

	// 判断是否添加空白行
	if (option.empty) {
		$(obj).append("<option value=''></option>");
	}

	// 添加下拉框内容
	$.grep(data, function(current, i) {
		var key = current[option.key];
		var value = current[option.text];
		if (key == def && value == option.dafault) {
			flag = true;
		}

		$(obj).append("<option value='" + key + "'>" + value + "</option>");
	});

	// 添加默认值
	if (!flag && option.dafault != false && def != "" && def != "0") {
		$(obj).append("<option value='" + def + "' selected='selected'>" + option.dafault + "</option>");
	} else {
		// 赋值
		$(obj).val(def);
	}
}

var MessagerProgressID=0;
/**
 * 显示进度条等待消息，延时300毫秒才显示loading界面
 */
function MessagerProgress(message) {
	if (message == 'close') {
//		$('#messager_progress').modal('hide');
		clearTimeout(MessagerProgressID);
		MessagerProgressID=0;
		$.messager.progress('close');
		return;
	}
	if (MessagerProgressID){
		return;
	}
	if (message == undefined || message == null || message == ""){
		message = "执行";
	}
	MessagerProgressID=setTimeout(function(){
		$.messager.progress({title:'Loading',msg:message});
	}, 300);
	
//	if ($('#messager_progress').length < 1) {
//		$("<div id='messager_progress' class='modal fade' role='dialog' style='top: 30%'> <div class='well well-large well-transparent lead' style='margin-bottom:0px;'> <i class='icon-spinner icon-spin icon-2x pull-left'></i> 请稍候,正在 " + message + " 中... </div> </div>").modal("show");
//	} else {
//		$('#messager_progress').modal("show");
//	}
}
/**
 * 获得数组的键值
 * 
 * @param array
 *            arr
 * @returns array
 */
function array_keys(arr) {
	return jQuery.map(arr, function(v, i) {
		return i;
	});
}
/**
 * 滚动到页面对应的ID位置
 * 
 * @param id
 * @returns {Boolean}
 */
function moveTo(id) {
	$body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
	if ($('#' + id).length) {
		$body.animate({
			scrollTop : $('#' + id).offset().top
		}, 500);
		return true;
	}
	return false;
}
/**
 * 保留小数位数
 * 
 * @param x
 * @param num
 */
function xround(x, num) {
	return Math.round(x * Math.pow(10, num)) / Math.pow(10, num);
}
/**
 * 文件下载
 * 
 * @param 文件对象
 * @param 上传地址
 */
function DownloadFile(file_name, file_path) {
	var href = window.location.pathname + "?controller=download";
	var parm = "&file_name=" + file_name + "&file_path=" + file_path;
	$.ajax({
		url : encodeURI(href + "&action=checkfile" + parm),
		type : "GET",
		async : false,
		success : function(msg) {
			if (msg != "") {
				$.messager.alert("错误", msg, "error");
			} else {
				window.location.href = href + parm;
			}
		}
	});
}