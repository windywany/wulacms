<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-suitcase"></i> 账户服务
		</h1>
	</div>
</div>
<section id="widget-grid">	
	<div class="row">		
		<article class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body no-padding">
				<table
					id="service-table"
					data-widget="nuiTable">
					<thead>
						<tr>					
							<th width="300">服务名</th>
							<th width="100">ID</th>
							<th class="hidden-xs hidden-sm">说明</th>
							<th width="100" class="text-align-right">状态</th>
						</tr>
					</thead>
					<tbody>
						{foreach $services as $service}
						<tr>
							<td>{$service.name}</td>
							<td>{$service.service}</td>
							<td class="hidden-xs hidden-sm">{$service.description|escape}</td>
							<td class="text-align-right">
                                {if $service.enabled}
                                    <a class="btn btn-xs btn-success" target="ajax" data-confirm="你真的要停用此项服务吗?"
                                       href="{'system/service'|app}disable/{$service.id}" title="点击停用">启用</a>
                                {else}
                                    <a class="btn btn-xs btn-warning" target="ajax"  data-confirm="你真的要启用此项服务吗?"
                                       href="{'system/service'|app}enable/{$service.id}" title="点击启用">停用</a>
                                {/if}
                            </td>
						</tr>
                        {foreachelse}
                        <tr>
                            <td colspan="4">
                                无服务
                            </td>
                        </tr>
						{/foreach}	
					</tbody>
				</table>
                </div>
			</div>
		</article>
	</div>
</section>