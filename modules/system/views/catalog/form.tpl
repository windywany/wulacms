<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-folder-open"></i> {$catalogTitle}
			<span>&gt; 编辑器</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default" href="#{'system/catalog'|app:0}{$catalogType}/" >
					<i class="glyphicon glyphicon-circle-arrow-left"></i> 返回
			</a>			
		</div>
	</div>
</div>

<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12">
			<div class="jarviswidget"
                id="wid-catalog-form"     
                data-widget-colorbutton="false"
				data-widget-editbutton="false"
				data-widget-togglebutton="false"
				data-widget-deletebutton="false"
				data-widget-fullscreenbutton="false"
				data-widget-custombutton="false"
				data-widget-collapsed="false"
				data-widget-sortable="false">
                <header>
                     <span class="widget-icon">
                          <i class="fa fa-edit"></i>
                     </span>
                     <h2> {$catalogTitle}编辑器 </h2>                     
                </header>
                <div>
                     <div class="widget-body widget-hide-overflow no-padding">
                          
                          <form name="CatalogForm"                          		
                          		data-widget="nuiValidate" action="{'system/catalog/save'|app}" 
                          		method="post" id="system-catalog-form" class="smart-form" target="ajax"
                          		>
                          	<input type="hidden" id="cataload_id" name="id" value="{$id}"/>
                          	<input type="hidden" name="type" value="{$catalogType}"/>
							<fieldset>											
								<div class="row">
									{if !$is_enum}
									<section class="col col-4">
										<label class="label">上级{$catalogTitle}</label>
										<input type="hidden" data-widget="nuiTreeview" style="width:100%" name="upid" id="upid"
											   value="{$upid}" placeholder="无"
											   data-text="{$uptext|escape}" data-source="{'system/ajax/treedata'|app}?table=catalog&params%5Btype%5D={$catalogType}&cid={$id}"/>
									</section>
									{/if}
									<section class="col {if $is_enum}col-8{else}col-4{/if}">
										<label class="label">{$catalogTitle}</label>
										<label class="input">									
										<input type="text" name="name" 
											id="name" value="{$name}"/>
										</label>
									</section>	
									<section class="col col-4">
										<label class="label">编号</label>
										<label class="input">				
										<input type="text" name="alias" 
											id="alias" value="{$alias}"/>
										</label>
									</section>														
								</div>								
								{if $widgets}								
								{$widgets|render}
								{/if}
								<section>
									<label class="label">说明</label>
									<label class="textarea">										
										<textarea rows="1" 
											name="note" id="note">{$note|escape}</textarea>
									</label>
								</section>								
							</fieldset>
							<footer>
								<button type="submit" class="btn btn-primary">
									保存
								</button>
								<a  class="btn btn-default" id="goto-catalist" href="#{'system/catalog'|app:0}{$catalogType}">
									返回
								</a>	
							</footer>
						</form>

                     </div>
                </div>
           </div>
		</article>
	</div>
</section>
<div id="catalog-next-step-dialog" class="hidden">	
	<p class="text-left">
	[<a href="javascript:void(0);" onclick="nUI.closeDialog('catalog-next-step-dg')">编辑</a>] &nbsp;
	[<a class="add-new-cata" href="#{'system/catalog/add'|app:0}{$catalogType}/{$upid}" onclick="nUI.closeDialog('catalog-next-step-dg')">再加一个] &nbsp;
	{if !$is_enum}
	[<a class="add-sub-cata" href="#{'system/catalog/add'|app:0}{$catalogType}" onclick="nUI.closeDialog('catalog-next-step-dg')">添加子{$catalogTitle}</a>] &nbsp;
	{/if}
	[<a href="#{'system/catalog'|app:0}{$catalogType}" onclick="nUI.closeDialog('catalog-next-step-dg')">返回</a>] &nbsp;
	</p>
</div>
<script type="text/javascript">
	var add_new_cata = $('#catalog-next-step-dialog .add-new-cata').attr('href');
	var add_sub_cata = $('#catalog-next-step-dialog .add-sub-cata').attr('href');
	nUI.validateRules['CatalogForm'] = {$rules};
	nUI.ajaxCallbacks.cataload_saved = function(args){		
		$('#cataload_id').val(args.id);		
		var tree = $('#upid').data('treeObj');
		if(tree){
			tree.setCid(args.id);
		}
		var dg = new nUI.Dialog('catalog-next-step-dg', '接下来你可以', {
            model : true,
            closable:false,      
            content:function(dg){
            	return $('#catalog-next-step-dialog').html();
            },
            onCreated:function(dg){
            	var t = (new Date()).getTime();
            	dg.body.find('.add-sub-cata').attr('href',add_sub_cata+'/'+args.id+'?_t='+t);
            	dg.body.find('.add-new-cata').attr('href',add_new_cata+'?_t='+t);
            }
        });
    	dg.openLocal();
	};			
</script>