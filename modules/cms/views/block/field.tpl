<form name="CustomCfgFieldForm"                          		
   		data-widget="nuiValidate" action="{'system/preference/custom'|app}" 
   		method="post" id="customfield-form" class="smart-form" target="ajax">                          	
   	<input type="hidden" name="cfg" value="{$cfg}"/>
	<fieldset>												
		<div class="row">
			<section class="col col-6">
				<label class="label">配置项字段</label>
				<label class="input">
				<input type="text" name="name" id="name" value="{$name}"/>
				</label>
			</section>
			<section class="col col-6">
				<label class="label">配置项名称</label>
				<label class="input">
				<input type="text" name="label" 
					id="label"  value="{$label}"/>
				</label>
			</section>
		</div>
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
					<input title="{$widget->getDataProvidor('')->getOptionsFormat()|escape}" type="radio" onclick="custom_rest_type_format(this)" name="type" value="{$wtype}" {if $type==$wtype}checked="checked"{/if}/>
					<i></i>{$widget->getName()}</label>
				{/if}
				{/foreach}
			</div>
		</section>
		<section>
			<label class="label">数据源(此组件可使用的数据)</label>
			<label class="textarea">
				<textarea rows="2" name="defaults" id="defaults">{$defaults|escape}</textarea>
			</label>
			<div class="note" id="defaults_help">{$defaultFormat}</div>
		</section>
	</fieldset>
	<footer>								
		<button type="submit" class="btn btn-primary">
			{if $field}更新{else}添加{/if}
		</button>
		<button type="reset" class="btn btn-default">
			重置
		</button>
	</footer>
</form>                     