//全选,全不选
$("#checkAll,#checkAllBottom").click(function (){
	if($(this).prop("checked") && !$(this).prop("disabled")){
		$("input[name='key']:enabled").prop("checked","checked");
	}else{
		$("input[name='key']:enabled").removeAttr("checked");
	}
});
//自定义按钮
$(".btn-fun").click(function() {
	var orgin_data = $(this).attr('orgin-data');
	var orgin_arr  = $.parseJSON(orgin_data);
	orgin_arr.id    = orgin_arr.id?orgin_arr.id:getSelectCheckboxValues();
	if (orgin_arr.id==''){
		Tips({info:'请选择记录！',type:3}); return false;
	}
	if (orgin_arr.type=='dialog') {
		window[orgin_arr.action](orgin_arr);
	}else if (orgin_arr.type=='delete') {
	//	delSelect(orgin_arr.id,this);
	}else{
		var arrstr = orgin_arr.text.split("/");
		$.ajax({
			type:"get",
			dataType:"json",
			url:orgin_arr.url,
			data:{id:encodeURI(orgin_arr.id)},
			beforeSend:function(){
				Tips({info:'正在处理,请稍后',type:1});
			},
			success:function(data){
				if (data.type==2 && arrstr.length==2) {
					$.each(data.listid,function(i,ids){
						var idsObj = $('input[type="checkbox"][name="key"][value='+ids.id+']').parent().parent();
						idsObj.find('.'+orgin_arr.action).attr('class','link_Btn_'+ids.status+' '+orgin_arr.action).text(arrstr[ids.status]);
					});
				};
				Tips(data);
			},
			error:function(){
				Tips({info:'unknown error!',type:3});
			}
		});
	}
 });
 //获取所有选中多选框值
 function getSelectCheckboxValues(){
 	var chk_value =[];
 	$('input[name="key"]:checked').each(function(){
 		  chk_value.push($(this).val());
 	});
 	return chk_value;
 }
	//排序
function sortBy (field,sort){
	var url = location.href;
	location.href = url.replace(/&_order=(.*)&_sort=(0|1)/g,"")+"&_order="+field+"&_sort="+sort;
}
//new
// 单个删除按钮或链接被点击产生的ajax事件
$(document).on('click', '.ajax-del', function(event) {
	event.preventDefault();
	var href = $(this).attr('data-url');
	var wind = dialog({
			content: '确认删除该项？<br/>删除后数据将无法恢复!',
			okValue: '确定',
			cancelValue: '取消',
			ok: function() {
				$.getJSON(href).done(function(data) {
					if (data.state === 'success' || data.status == 1) {
						location.href = location.href;
					} else if (data.state === 'fail' || data.status == 0) {
						alert(data.info);
					}
				});
			},
			cancel: true
		});
	wind.show(this);
	return false;
});
//显示二级
$('.showSubNav').click(function(event) {
	var that                = $(this),
	left_menu               = $('.leftmenu_projects_list'),
	aid                     = that.children('a').attr('data-id'),
	bid                     = left_menu.attr('data-id'),
  leftmenu_projects_html = '';
	if(aid!=bid){
		var atitle = that.children('a').attr('title');
		$('.leftmenu_projects').children('dt').html(atitle+'<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i>').attr('data-id',aid);
		//var $('.leftmenu_projects')..children('dt').html('<dt>'+atitle+' <a href="javascript:;" class="pull-right" ><i class="fa fa-plus"></i></a></dt>');
		if(aid=='member'){
			$('#version_info').show();
		}else{
			$('#version_info').hide();
		}
		//组织
		 $.ajax({
		 	url: '?m=admin&c=ajax&a=getMenu',
		 	type: 'POST',
		 	dataType: 'json',
		 	data: {id: aid}
		 })
		 .done(function(data) {
			 if( data.length > 0){
					 $.each(data,function(index, el) {
					 	 leftmenu_projects_html +='<dd class="leftmenu_projects_dd"><h3><a href="'+el.url+'"><i class="fa '+el.icon+'" style="color: '+el.color+';"></i> '+el.name+'</a></h3>';
						 if(el.lev3){
							 leftmenu_projects_html +='<ul class="ui-sortable">';
							 $.each(el.lev3,function(index2, el2) {
								 if(aid=='member'){
									 leftmenu_projects_html +='<li><a href="'+el2.url+'" target="_blank"><i class="fa '+el2.icon+'" style="color: '+el2.color+';"></i> '+el2.name+'</a></li>';
								 }else{
									 leftmenu_projects_html +='<li><a href="'+el2.url+'"><i class="fa '+el2.icon+'" style="color: '+el2.color+';"></i> '+el2.name+'</a></li>';
								 }
							 });
							 leftmenu_projects_html +='</ul>';
						 }
					 	 leftmenu_projects_html += '</dd>';
					 });
			 }
		 	 //console.log(data);
			 left_menu.html(leftmenu_projects_html);
			 left_menu.attr('data-id',aid);
		 })
		 .fail(function() {
			 $('.leftmenu_projects').children('dt').html('系统加载异常，<br/>请重试！').attr('data-id',aid);
			 left_menu.html('');
		 })
		 .always(function() {
		 	$(".fa-spinner").hide();
		 });
	}

	var leftpanel_width  = 	$('.leftpanel').css('width');
	var leftcontent_width = '290px';
	if (leftpanel_width=='120px') {
		leftcontent_width = '360px';
	}

	$('#leftmenu_content').animate({left:leftcontent_width,opacity:'1'},'fast',function(){
		$('#leftmenu_backdrop').show();
		$('.popbox').hide();
		$('.leftmenu_module>li>a').removeClass('active');
		that.children('a').addClass('active');
	});

});
//隐藏二级
$('#leftmenu_backdrop').click(function(event) {
	$('#leftmenu_content').animate({left:'0px',opacity:'0'},'fast',function(){
		$('#leftmenu_backdrop').hide();
		$('.popbox').hide();
	});
});
//显示快捷方式
$('#shortcut_create_btn').click(function(event) {
	$('.popbox').show();
	$('#leftmenu_content').animate({left:'0px'});
	$('#leftmenu_backdrop').show();

	var leftpanel_width  = 	$('.leftpanel').css('width');
	var popbox_status = $('.popbox').css('display');
	if (popbox_status=='block') {
		if (leftpanel_width=='50px') {
				$('.popbox').animate({left:'50px',opacity:'1'},'fast',function(){});
		}else{
			  $('.popbox').animate({left:'120px',opacity:'1'},'fast',function(){});
		}
	}

});

windowResize();
$(window).resize(function() {
	windowResize();
});
function windowResize(){
	//右侧最小高度
	$(".frame").css("min-height",$(document.body).height()-$('.mod-navbar').height()-31);
	//菜单自动隐显
	if($(document.body).width()<=500){
		$('.centerpanel').css({'margin-left':"0"});
		$('.leftpanel').hide();
		$('.showOnNav').show();
	}else{
		$('.centerpanel').css({'margin-left':$('.leftpanel').css('width')});
		$('.leftpanel').show();
		$('.showOnNav').hide();
	}
}
//边栏隐显
//$(document).on('click', '.showOnNav', function(event) {
$('.showOnNav').click(function(event) {
	event.preventDefault();
	var display  = 	$('.leftpanel').css('display');
	if (display=='block') {
		$('.centerpanel').css({'margin-left':"0"});
		$('.leftpanel').hide();
		$('.showOnNav').children('i').removeClass('fa-remove').addClass('fa-reorder');
	}else{
		$('.centerpanel').css({'margin-left':$('.leftpanel').css('width')});
		$('.leftpanel').show();
		$('.showOnNav').children('i').removeClass('fa-reorder').addClass('fa-remove');
	}
});
//边栏宽窄转换
$(document).on('click', '.setNavWidth', function(event) {
	event.preventDefault();
	var leftpanel_width  = 	$('.leftpanel').css('width');
	if (leftpanel_width=='50px') {
		$('.leftpanel').css('width', '120px');
		$('.centerpanel').css({'margin-left':"120px"});
		$('#leftmenu_tab').css('width', '120px');
		$('.nav-text-desc').show();
		$('.setNavWidth').find('a>i').removeClass('fa-arrow-right').addClass('fa-arrow-left');
	}else{
		$('.leftpanel').css('width', '50px');
		$('.centerpanel').css({'margin-left':"50px"});
		$('#leftmenu_tab').css('width', '50px');
		$('.nav-text-desc').hide();
		$('.setNavWidth').find('a>i').removeClass('fa-arrow-left').addClass('fa-arrow-right');
	}
	//二级菜单
	var leftmenu_content_left = $('#leftmenu_content').css('left');
	if (leftmenu_content_left!='0px') {
		if (leftpanel_width=='50px') {
				$('#leftmenu_content').animate({left:'360px',opacity:'1'},'fast',function(){});
		}else{
			  $('#leftmenu_content').animate({left:'290px',opacity:'1'},'fast',function(){});
		}
	}
	//快捷菜单
	var popbox_status = $('.popbox').css('display');
	if (popbox_status=='block') {
		if (leftpanel_width=='50px') {
				$('.popbox').animate({left:'120px'},'fast');
		}else{
				$('.popbox').animate({left:'50px'},'fast');
		}
	}
});
//用户详情
function showUserItem(id){
	var d = dialog({
		title: '用户详情',
		width:400,
		height:600,
		url:'?m=Admin&c=user&a=item&id='+id,
		quickClose: true,
		padding: 10,
		okValue: '关闭',
		ok: function () {},
    	cancel: false
	}).showModal();
}
/**
 * [Tips 信息提示窗]
    {
        id:'tips',                  //默认消息ID
        type:1,                     //消息类型[1:普通提示,2:成功,3:失败]
        info:'Loading...',          //提示信息
        href:null,                  //刷新地址
        time:8,                     //显示时间
        opacity:0.9,                //透明度
        target:'default-main-iframe',//打开方式
        tipheight:35,               //高度 px
        starttop:300,                //距离顶端高度(开始) px
        endtop:10,                   //距离顶端高度(结束) px
        fontsize:14,                //字体大小 px
        bgcolor:'#5884B7',          //背景颜色
        appear:1,                   //动画效果
        fun:''                      //回调
    }
 */
function Tips(obj){
	ToolTips.show(obj);
}
