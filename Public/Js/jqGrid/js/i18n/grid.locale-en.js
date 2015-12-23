;(function($){
/**
 * jqGrid English Translation
 * Tony Tomov tony@trirand.com
 * http://trirand.com/blog/
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
**/
$.jgrid = $.jgrid || {};
$.extend($.jgrid,{
	defaults : {
		recordtext: "显示 {0} - {1} 总记录数 {2}",
		emptyrecords: "没有记录",
		loadtext: "载入中...",
		pgtext : "当前页 {0} 页数 {1}"
	},
	search : {
		caption: "报表检索",
		Find: "开始检索",
		Reset: "重置",
		odata : ['等于', '不等于', '小于', '小于等于','大于','大于等于', '开始','不开始','存在','不存在','结束','不结束','包含','不包含'],
		groupOps: [	{ op: "AND", text: "所有满足" },	{ op: "OR",  text: "任意满足" }	],
		matchText: " match",
		rulesText: " rules"
	},
	edit : {
		addCaption: "添加记录",
		editCaption: "编辑记录",
		bSubmit: "提交",
		bCancel: "取消",
		bClose: "关闭",
		saveData: "数据已经改变，是否保存?",
		bYes : "是",
		bNo : "否",
		bExit : "退出",
		msg: {
			required:"字段必须",
			number:"请输入合法数字",
			minValue:"必须大于或等于 ",
			maxValue:"必须小于或等于",
			email: "不是合法邮箱",
			integer: "请输入一个整数",
			date: "不是有效的日期",
			url: "不是有效的URL('http://' or 'https://')",
			nodefined : "没有定义",
			novalue : " return value is required!",
			customarray : "Custom function should return array!",
			customfcheck : "Custom function should be present in case of custom checking!"

		}
	},
	view : {
		caption: "记录数",
		bClose: "关闭"
	},
	del : {
		caption: "删除",
		msg: "删除选中的记录数(s)?",
		bSubmit: "删除",
		bCancel: "取消"
	},
	nav : {
		edittext: "",
		edittitle: "编辑选中行",
		addtext:"",
		addtitle: "添加新行",
		deltext: "",
		deltitle: "删除选中行",
		searchtext: "",
		searchtitle: "搜索记录",
		refreshtext: "",
		refreshtitle: "重新载入",
		alertcap: "",
		alerttext: "",
		viewtext: "",
		viewtitle: "查看选择行"
	},
	col : {
		caption: "选择列",
		bSubmit: "确定",
		bCancel: "取消"
	},
	errors : {
		errcap : "错误",
		nourl : "没有设置url",
		norecords: "No records to process",
		model : "Length of colNames <> colModel!"
	},
	formatter : {
		integer : {thousandsSeparator: " ", defaultValue: '0'},
		number : {decimalSeparator:".", thousandsSeparator: " ", decimalPlaces: 2, defaultValue: '0.00'},
		currency : {decimalSeparator:".", thousandsSeparator: " ", decimalPlaces: 2, prefix: "", suffix:"", defaultValue: '0.00'},
		date : {
			dayNames:   [
				"Sun", "Mon", "Tue", "Wed", "Thr", "Fri", "Sat",
				"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
			],
			monthNames: [
				"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
				"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
			],
			AmPm : ["am","pm","AM","PM"],
			S: function (j) {return j < 11 || j > 13 ? ['st', 'nd', 'rd', 'th'][Math.min((j - 1) % 10, 3)] : 'th'},
			srcformat: 'Y-m-d',
			newformat: 'd/m/Y',
			masks : {
				ISO8601Long:"Y-m-d H:i:s",
				ISO8601Short:"Y-m-d",
				ShortDate: "n/j/Y",
				LongDate: "l, F d, Y",
				FullDateTime: "l, F d, Y g:i:s A",
				MonthDay: "F d",
				ShortTime: "g:i A",
				LongTime: "g:i:s A",
				SortableDateTime: "Y-m-d\\TH:i:s",
				UniversalSortableDateTime: "Y-m-d H:i:sO",
				YearMonth: "F, Y"
			},
			reformatAfterEdit : false
		},
		baseLinkUrl: '',
		showAction: '',
		target: '',
		checkbox : {disabled:true},
		idName : 'id'
	}
});
})(jQuery);
