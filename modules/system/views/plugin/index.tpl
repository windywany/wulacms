<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-puzzle-piece"></i> 插件管理			
		</h1>
	</div>	
</div>

<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12">
			<div class="panel panel-default">
				<ul class="nav nav-tabs in" id="plugin-type-tab">
					<li {if $status=='installed'}class="active"{/if}>
						<a href="#{'system/plugin'|app:0}installed/" class="txt-color-green"><i class="fa fa-puzzle-piece"></i> <span class="hidden-mobile hidden-tablet">已安装</span></a>
					</li>
					<li {if $status=='uninstalled'}class="active"{/if}>
						<a href="#{'system/plugin'|app:0}uninstalled/" class="txt-color-blueLight"><i class="fa fa-puzzle-piece"></i> <span class="hidden-mobile hidden-tablet">未安装</span></a>
					</li>
					<li {if $status=='upgrade'}class="active"{/if}>
						<a href="#{'system/plugin'|app:0}installed/?status=upgrade" class="txt-color-orange" rel="upgrade"><i class="fa fa-puzzle-piece"></i> <span class="hidden-mobile hidden-tablet">可升级</span></a>
					</li>
					<li {if $status=='disabled'}class="active"{/if}>
						<a href="#{'system/plugin'|app:0}installed/?status=disabled" class="txt-color-redLight" rel="disable"><i class="fa fa-puzzle-piece"></i> <span class="hidden-mobile hidden-tablet">已禁用</span></a>
					</li>
					<li {if $status=='system'}class="active"{/if}>
						<a href="#{'system/plugin'|app:0}installed/?status=system" class="txt-color-magenta" rel="upgrade"><i class="fa fa-gears"></i> <span class="hidden-mobile hidden-tablet">系统内核</span></a>
					</li>
				</ul>
				<form data-widget="nuiSearchForm" data-for="#plugin-table" class="smart-form">
				  		<fieldset>	
				  			<div class="row">
				  				<section class="col col-6">
				  					<label class="input">
										<i class="icon-prepend fa fa-filter"></i>
										<input type="text" placeholder="插件名" name="plname"/>
									</label>
				  				</section>  				
				  				<section class="col col-1">
									<button class="btn btn-sm btn-primary" type="submit">
										<i class="fa fa-search"></i> 搜索
									</button>
								</section>
							</div>
						</fieldset>	
				  	</form>
				<table 
					id="plugin-table"
					data-widget="nuiTable"
					data-auto="true"
					data-source="{'system/plugin/data'|app}?installed={$installed}&status={$status}">
					<thead>
						<tr>									
							<th width="400">插件名</th>							
							<th width="80">安装版本</th>							
							<th class="hidden-xs hidden-sm">描述</th>
							<th width="140"></th>							
						</tr>
					</thead>
				</table>	
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#plugin-table" data-limit="10"></div>
				</div>			
			</div>
		</article>
	</div>
</section>