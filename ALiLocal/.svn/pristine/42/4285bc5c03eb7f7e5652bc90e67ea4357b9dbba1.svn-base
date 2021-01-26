function cnVeryCalendar()
{
/*Style*/
var border_frame   = '#000000';
var border_inner   = '#ffffff';
var fore_frameCaption = '#ffffff';
var back_frameCaption = '#4F4F4F';
var fore_currentMonth = '#EB0A0A';
var back_weekName   = '#ffffff';
var back_nullDay   = '#ffffff';
var fore_dayMouseOver = '#ff0000';
var back_dayMouseOver = '#D4D4D4';
var font_cnChar   = 'font-family:宋体,sans-serif; font-size:12px;';
var font_numChar   = 'font-family:tahoma,arial,sans-seirf; font-size:11px;';
var style_cell   = 'line-height:14px; border-color:' + border_inner;
var style_gray   = 'line-height:14px;color:#B4B4B4; border-color:' + border_inner;
var today_decoration = "font-weight:bold;color:#EB0A0A;";
/*"font-weight:bold"*/

/*Declare*/
var reciever;
var today = new Date();
var y = today.getFullYear();
var m = today.getMonth() + 1;


/*Return Max Days In The Month*/
this.daysInMonth = function(y, m)
{
   switch (m)
   {
    case 1:
    case 3:
    case 5:
    case 7:
    case 8:
    case 10:
    case 12:
     return 31;
    case 4:
    case 6:
    case 9:
    case 11:
     return 30;
    case 2:
     /*Is Leep Year*/
     if (y % 4 != 0)
     {
      return 28;
     }
     if (y % 100 == 0)
     {
      return y % 400 == 0 ? 29 : 28;
     }
     return 29;
   }
}


/*Generate Codes*/
this.generateCalendarTable = function()
{
   var i;
   var j = new Date(y, m-1, 1).getDay();
   var k = this.daysInMonth(y, m);
   var body = '';

   /*Frame Table Header*/
   body += "<table align='center' cellpadding='1' cellspacing='1' width='100%' height='100%' style='border:1px " + border_frame + " solid; background:white;'>";
   body += " <tr>";
   body += "   <td style='background:" + back_frameCaption + ";" + font_cnChar + "' height='20'>";
   body += "    <div style='color:" + fore_frameCaption + "; float:left'>&nbsp;选择出发日期</div>";
   body += "    <div style='float:right'>";
   body += "     <a href=\"javascript:calendar.setValue('')\" style='color:" + fore_frameCaption + "; text-decoration:none;" + font_cnChar + "'>[擦除]</a> ";
   body += "     <a href='javascript:calendar.fadeOut()' style='color:" + fore_frameCaption + "; text-decoration:none;" + font_cnChar + "'>[关闭]</a>";
   body += "    </div>";
   body += "   </td>";
   body += " </tr>";
   body += " <tr>";
   body += "   <td style='padding-bottom:0px'>";
   body += "    <table align='center' width='99%' cellpadding='0' cellspacing='0'>";
   body += "     <tr>";
   body += "      <td><a hef='#' onclick='calendar.loadPreviousYear()' style='cursor:pointer;color:#ff0000;font-weight:bold'>&lt;&lt;</a>&nbsp;年&nbsp;<a hef='#' onclick='calendar.loadNextYear()' style='cursor:pointer;color:#ff0000;font-weight:bold'>&gt;&gt;</a></td>";
   body += "      <td align='center' nowrap='nowrap' style='color:" + fore_currentMonth + ";" + font_cnChar + "'><b>"+ y + "年" + m + "月</b></td>";
   body += "      <td align='right'><a hef='#' onclick='calendar.loadPreviousMonth()' style='cursor:pointer;color:#ff0000;font-weight:bold'>&lt;&lt;</a>&nbsp;月&nbsp;<a hef='#' onclick='calendar.loadNextMonth()' style='cursor:pointer;color:#ff0000;font-weight:bold'>&gt;&gt;</a></td>";
   body += "     </tr>";
   body += "    </table>";
   body += "   </td>";
   body += " </tr>";
   body += " <tr>";
   body += "   <td>";

   /*Calendar Table Header*/
   body += "<table align='center' width='99%' cellpadding='3' cellspacing='0' border='1' bordercolor='" + border_inner + "' style='border-collapse:collapse; table-layout:fixed;'>";
   body += " <tr align='center' style='background:" + back_weekName + "'>";
   body += "   <td style='" + font_cnChar + style_cell + "'>日</td>";
   body += "   <td style='" + font_cnChar + style_cell + "'>一</td>";
   body += "   <td style='" + font_cnChar + style_cell + "'>二</td>";
   body += "   <td style='" + font_cnChar + style_cell + "'>三</td>";
   body += "   <td style='" + font_cnChar + style_cell + "'>四</td>";
   body += "   <td style='" + font_cnChar + style_cell + "'>五</td>";
   body += "   <td style='" + font_cnChar + style_cell + "'>六</td>";
   body += " </tr>";

   /*Insert Null Days Before The First Day In Current Month*/
   if (j != 0)
   {
    body += "<tr align='center'>";
    body += ("<td style='background:" + back_nullDay + style_cell + "' colspan='" + j + "'></td>");
   }

   /*Loop Each Days In Current Month*/
   for (i=1; i<=k; i++)
   {
    /*Row Begin*/
    if ((i+j) % 7 == 1)
    {
     body += "<tr align='center'>";
    }

    /*Cells Day By Day*/
    body += "<td";
    var mTemp=m;
	if(mTemp<10){
		mTemp="0"+mTemp;
	}
	var iTemp=i;
	if(iTemp<10){
		iTemp="0"+iTemp;
	}
	var numYearMonthDate=y*10000 + m*100 + i;
	var nowYearMonthDate=today.getFullYear()*10000+(today.getMonth()+1)*100+today.getDate();
	var numYearMonth=y*100 + m;
	var nowYearMonth=today.getFullYear()*100+(today.getMonth()+1);

	body += " onmouseover=\"this.style.backgroundColor='" + back_dayMouseOver + "'; this.style.color='" + fore_dayMouseOver + "'\"";
	body += " onmouseout=\"this.style.backgroundColor=''; this.style.color=''\"";
	body += " onclick=\"calendar.setValue('" + y +'-' + mTemp+'-' + iTemp + "')\"";
	body += " style='cursor:pointer; " + font_numChar + style_cell + "'";

	
    body += ">" + i + "</td>";

    /*Row End*/
    if ((i+j) % 7 == 0)
    {
     body += ("</tr>");
    }
   }

   /*Append Null Days After The Last Day In Current Month*/
   if ((i+j) % 7 != 0)
   {
    body += ("<td style='background:" + back_nullDay + style_cell + "' colspan='" + (8 - (i+j)%7) + "'></td>");
    body += ("</tr>");
   }
   if (j < (36-k))
   {
    body += ("<tr><td colspan='7' style='background:" + back_nullDay + style_cell + "'>&nbsp;</td></tr>");
   }
   if (j == 0 && k == 28)
   {
    body += ("<tr><td colspan='7' style='background:" + back_nullDay + style_cell + "'>&nbsp;</td></tr>");
   }

   /*End Calendar Table*/
   body += "</table>";

   /*End Frame Table*/
   body += "</td></tr></table>";

   /*Return*/
   return body;
}


/*Load Previous Year*/
this.loadPreviousYear = function()
{
   y--;
   document.getElementById("__cnVeryCalendarContainer").innerHTML = this.generateCalendarTable();
}
this.loadNextYear = function()
{
   y++;
   document.getElementById("__cnVeryCalendarContainer").innerHTML = this.generateCalendarTable();
}
this.loadPreviousMonth = function()
{
   m--;
   if (m < 1)
   {
    m = 12;
    y--;
   }
   document.getElementById("__cnVeryCalendarContainer").innerHTML = this.generateCalendarTable();
}
this.loadNextMonth = function()
{
   m++;
   if (m > 12)
   {
    m = 1;
    y++;
   }
   document.getElementById("__cnVeryCalendarContainer").innerHTML = this.generateCalendarTable();
}


/*Get Position*/
this.getAbsolutePosition = function(element)
{
   var point = { x: element.offsetLeft, y: element.offsetTop };
   /*Recursion*/
   if (element.offsetParent)
   {
    var parentPoint = this.getAbsolutePosition(element.offsetParent);
    point.x += parentPoint.x;
    point.y += parentPoint.y;
   }
   return point;
};


/*Pop Layer*/
this.setHook = function(dateField)
{
 
   if (document.getElementById("__cnVeryCalendarContainer").style.display != 'none' && reciever.id == dateField.id)
   {
    document.getElementById("__cnVeryCalendarContainer").style.display = 'none';
    return;
   }
   reciever = dateField;

   /*-- 如果不想在第二次打开日历时回归为当前月，则把下面两行注释掉或删掉 --*/
   y = today.getFullYear();
   m = today.getMonth() + 1;
   /*----------------------------*/

   var point = this.getAbsolutePosition(dateField);
  /* document.getElementById("__cnVeryCalendarContainer").style.left = (point.x + dateField.offsetWidth + 5) + 'px';*/
  /* document.getElementById("__cnVeryCalendarContainer").style.top = point.y + 'px';*/
   document.getElementById("__cnVeryCalendarContainer").style.left = (point.x ) + 'px';
   document.getElementById("__cnVeryCalendarContainer").style.top =  (point.y + 20) + 'px';
   document.getElementById("__cnVeryCalendarContainer").innerHTML = this.generateCalendarTable();
   document.getElementById("__cnVeryCalendarContainer").style.display = '';
}


/*Hide Layer*/
this.fadeOut = function()
{
   document.getElementById("__cnVeryCalendarContainer").style.display = 'none';
}


/*Click a Day Cell To Add The Value*/
this.setValue = function(date)
{
   reciever.oldValue=reciever.value;
   reciever.value = date;
   $(reciever).trigger('change');
   this.fadeOut();
}
}
var hhttmmll="<div id='__cnVeryCalendarContainer' style='width:200px; height:190px; position:absolute; float:left; display:none; z-index:1002'></div>";
/*Render Instance*/
if (typeof(jQuery) != 'undefined'){
	$(function(){
		$(document.body).append(hhttmmll);
	})
}else{
	window.onload=function(){
		document.body.innerHTML=document.body.innerHTML+hhttmmll;
	}
}
var calendar = new cnVeryCalendar();

function control_datebox_kd(event,obj){
	$(obj).data('val',obj.value);
}
function control_datebox_ku(event,obj){
	//27,46,8 delete backdelete
	//9 tab
	/*
	if ($.inArray(event.keyCode,[27,46,8]) >-1){
		return;
	}
	if ($.inArray(event.keyCode,[48,49,50,51,52,53,54,55,56,57,96,97,98,99,100,101,102,103,104,105,9,91,92,37,39]) >-1){
		
		if (obj.value.length ==4){
			obj.value += '-';
		}else if (obj.value.length ==7){
			obj.value += '-';
		}else if (obj.value.length >10 && $.inArray(event.keyCode,[,9,91,92,37,39]) == -1){
			obj.value=$(obj).data('val');
		}
	}else {
		obj.value=$(obj).data('val');
	}
	*/
}
function control_datebox_change(event,obj){
	if (obj.value.length !=10){
		obj.value='';obj.defaultValue;
		calendar.setHook(obj);
		return;
	}
	if (strDateTime(obj.value) ==false){
		obj.value='';//obj.defaultValue;
		calendar.setHook(obj);
		return;
	}
}
function strDateTime(str) 
{ 
	var r = str.match(/^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/); 
	if(r==null)return false; 
	var d= new Date(r[1], r[3]-1, r[4]); 
	return (d.getFullYear()==r[1]&&(d.getMonth()+1)==r[3]&&d.getDate()==r[4]); 
} 