$.extend($.fn.validatebox.defaults.rules, {
	dateFormate : {
		validator : function(value) {
			var v = value.replace(/\D/g, '');
			var result = "";

			if (v.length > 8) {
				v = v.substring(0, 8);
			}

			if (v.length <= 4) {
				result = v;
			} else if (v.length > 4) {
				var year = v.substring(0, 4);
				var right = v.substring(4);
				if (right.length <= 2) {
					result = year + "-" + right;
				} else {
					var month = right.substring(0, 2);
					right = right.substring(2);
					result = year + "-" + month + "-" + right;
				}
			}

			this.value = result;
			var time = Date.parse(result);
			if (result.length >= 10 && !isNaN(time)) {
				return true;
			} else {
				return false;
			}
		},
		message : '日期格式不正确.'
	},
	checkExist : {
		validator : function(value, param) {
			var result = false;
			$.ajax({
				url : param[0] + "?code=" + encodeURIComponent(value),
				type : "GET",
				async : false,
				success : function(msg) {
					if (msg == "true")
						result = true;
				}
			});
			return result;
		},
		message : "输入内容不存在或被禁用,请重新输入或从下拉列表框中选择"
	},
	discountBox : {
		validator : function(value) {
			if ($.trim(value) == "0") {
				this.value = "1";
			}
			return true;
		}
	}
});
