<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-list-alt"></i> {$blockName}
			<span>&gt; 编辑自定义字段</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a id="gobackmodel"  class="btn btn-default btn-labeled" href="#{'cms/blockfield'|app:0}{$block}">
				<span class="btn-label">
					<i class="glyphicon glyphicon-circle-arrow-left"></i>
				</span> 返回
			</a>			
		</div>
	</div>
</div>

<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12">
			<div class="jarviswidget"
                id="wid-blockfield-w"     
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
                     <h2> 自定义字段编辑器 </h2>                     
                </header>
                <div>
                     <div class="widget-body widget-hide-overflow no-padding">
                          
                          <form name="BlockFieldForm"                          		
                          		data-widget="nuiValidate" action="{'cms/blockfield/save'|app}" 
                          		method="post" id="modelfield-form" class="smart-form" target="ajax"
                          		>
                          	<input type="hidden" name="id" value="{$id}"/>
                          	<input type="hidden" name="block" value="{$block}"/>
							<fieldset>												
								<div class="row">
									<section class="col col-6">
										<label class="label">字段(最好以field_开头)</label>
										<label class="input">
										<input type="text" name="name" 
											id="name" value="{$name}" {if $id}readonly="readonly"{/if}/>
										</label>
									</section>
									<section class="col col-6">
										<label class="label">名称</label>
										<label class="input">
										<input type="text" name="label" 
											id="label" value="{$label}"/>
										</label>
									</section>
								</div>
								<div class="row">
									<section class="col col-6">
										<label class="label">提示文本</label>
										<label class="input">
										<input type="text" name="tip" 
											id="tip" value="{$tip}"/>
										</label>
									</section>
									<section class="col col-6">
										<label class="label">默认值</label>
										<label class="input">
										<input type="text" name="default_value" 
											id="default_value" value="{$default_value}"/>
										</label>
									</section>
								</div>
								<section>
									<div class="inline-group">
										<label class="checkbox">
											<input type="checkbox" name="required" {if $required}checked="checked"{/if}/>
											<i></i>必须填写字段
										</label>
									</div>									
								</section>											
								<section>
									<span class="timeline-seperator text-center text-primary"> 
										<span class="font-sm">输入表单</span> 
									</span>
								</section>
								<div class="row">
									<section class="col col-4">
										<label class="label">所在组</label>
										<label class="input">
										<input type="text" name="group" 
											id="group" value="{$group}"/>
										</label>
										<div class="note">同一组的组件将在同一行显示.</div>
									</section>
									<section class="col col-4">
										<label class="label">宽度</label>
										<label class="input">
										<input type="text" name="col" 
											id="col" value="{$col}"/>
										</label>
										<div class="note">同一组的宽度加起来的和应该小于等于12.</div>
									</section>
									<section class="col col-4">
										<label class="label">排序</label>
										<label class="input">
										<input type="text" name="sort" 
											id="sort" value="{$sort}"/>
										</label>
										<div class="note">字段在表单中出现顺序，越小越靠前.</div>
									</section>
								</div>
								<section>				
									<label class="label">输入组件</label>					
									<div class="inline-group">
										{foreach $widgets as $wtype => $widget}
										{if $widget->getName()}
										<label class="radio">
											<input title="{$widget->getDataProvidor('')->getOptionsFormat()|escape}" type="radio" name="type" value="{$wtype}" {if $type==$wtype}checked="checked"{/if}/>
											<i></i>{$widget->getName()}</label>
										{/if}
										{/foreach}
									</div>
								</section>
								<section>
									<label class="label">数据源(此组件可使用的数据)</label>
									<label class="textarea">
										<textarea rows="4" name="defaults" id="defaults">{$defaults|escape}</textarea>
									</label>
									<div class="note" id="defaults_help">{$defaultFormat}</div>
								</section>
							</fieldset>							
							<footer>								
								<button type="submit" class="btn btn-primary">
									保存
								</button>
									<a  class="btn btn-default" href="#{'cms/blockfield'|app:0}{$block}">返回</a>
							</footer>
						</form>

                     </div>
                </div>
           </div>
		</article>
	</div>
</section>
<script type="text/javascript">
	nUI.validateRules['BlockFieldForm'] = {$rules};
	$('input[name="type"]').click(function(){
		$('#defaults').val('');
		$('#defaults_help').html($(this).attr('title'));
	});
</script>