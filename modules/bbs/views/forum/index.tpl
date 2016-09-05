<div class="inbox-nav-bar no-content-padding">
	<h1 class="page-title txt-color-blueDark hidden-tablet"><i class="fa fa-fw fa-sitemap"></i> 论坛版块</h1>
	
	<div class="btn-group pull-right inbox-paging" id="forum-toolbar">
		<a href="javascript:void(0);" class="btn btn-default btn-sm"><strong><i class="fa fa-chevron-left"></i></strong></a>
		<a href="javascript:void(0);" class="btn btn-default btn-sm"><strong><i class="fa fa-chevron-right"></i></strong></a>
	</div>
	<div class="btn-group pull-right inbox-paging hidden" id="thread-toolbar">
		<a href="javascript:void(0);" class="btn btn-default btn-sm"><strong><i class="fa fa-chevron-left"></i></strong></a>
		<a href="javascript:void(0);" class="btn btn-default btn-sm"><strong><i class="fa fa-chevron-right"></i></strong></a>
	</div>
	<div class="btn-group pull-right inbox-paging hidden" id="post-toolbar">
		<a href="javascript:void(0);" class="btn btn-default btn-sm"><strong><i class="fa fa-chevron-left"></i></strong></a>
		<a href="javascript:void(0);" class="btn btn-default btn-sm"><strong><i class="fa fa-chevron-right"></i></strong></a>
	</div>
</div>
<div class="inbox-body no-content-padding">
	<div class="inbox-side-bar">
		<table 
			id="forum-tree"
			class="inbox-table"
			data-widget="nuiTable"		
			data-no-hover="true"
			data-folderIcon2="fa fa-caret-right"
			data-folderIcon1="fa fa-caret-down"	
			data-source="{'bbs/forum/data'|app}"			
			data-tree="true">
			{include './data.tpl'}
		</table>
		<div class="air air-bottom inbox-space">
			<strong>{if $cnt}共{$cnt}个版块{else}请添加版块{/if}</strong><a href="{'bbs/forum/add'|app}" target="tag" data-tag="#forum-body" class="pull-right txt-color-green"><i class="fa fa-plus-square fa-lg"></i></a>
			<div class="progress progress-micro">
				<div class="progress-bar progress-primary" style="width: 100%;"></div>
			</div>
		</div>
	</div>
	<div class="table-wrap custom-scroll">		
		<div id="forum-body" class="tab-pane fade active in">
			  <p class="lead">请选择一个版块以开始操作或点击左下角<i class="fa fa-plus-square txt-color-green"></i>添加新版块.</p>	
		</div>
	</div>
</div>

<script type="text/javascript">
	var tableOpts = { 'target':'td.forumname','wrapper':'<div></div>','buttons':[] };
	tableOpts.buttons[0] = {			
		'html':'<a><i class="fa fa-pencil-square-o fa-lg txt-color-blue"></i></a>',
		onClick:function(elem) {
			alert(elem.attr('rel'))
		} 
	} ;
	tableOpts.buttons[1] = {			
			'html':'<a><i class="fa fa-plus-square txt-color-green fa-lg"></i></a>',
			onClick:function(elem) {
				alert(elem.attr('rel'))
			} 
		} ;
	tableOpts.buttons[2] = {			
		'html':'<a><i class="fa fa-trash-o txt-color-red fa-lg"></i></a>',
		onClick:function(elem) {
			alert(elem.attr('rel'))
		} 
	} ;
	$('#forum-tree').on('mouseenter','tr',function(){		
		nUI.showButtons(tableOpts,$(this));
	});
	
	$('#forum-tree').on('mouseleave','tr',function(){
		nUI.hideButtons(tableOpts);
	});
	
</script>