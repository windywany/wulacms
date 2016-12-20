<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-group"></i> 用户组管理			
		</h1>
	</div>
    {if $canAddGroup}
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a class="btn btn-success"
			   href="#{'system/group/add'|app:0}0/{$type}"><i class="glyphicon glyphicon-plus"></i> 新增
			</a>			
		</div>
	</div>
    {/if}
</div>
<section id="widget-grid">	
	<div class="row">		
		<article class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body no-padding">
					<ul class="nav nav-tabs in" id="role-type-tab">
						{foreach $types as $t=>$tname}
						<li {if $type==$t}class="active"{/if}>
							<a href="#{'system/group'|app:0}{$t}" class="txt-color-blue"><i class="fa fa-group"></i> <span class="hidden-mobile hidden-tablet">{$tname}</span></a>
						</li>
						{/foreach}
					</ul>
				<table
					id="group-table"
					data-widget="nuiTable">
					<thead>
						<tr>					
							<th width="300">用户组名</th>
							<th width="80">ID</th>
                            <th width="100">等级</th>
                            <th width="80">限值</th>
                            <th width="180">等级名称</th>
							<th class="hidden-xs hidden-sm">说明</th>
							<th width="80"></th>
						</tr>
					</thead>
					<tbody>
						{foreach $groups as $group}
						<tr data-parent="true" rel="{$group.group_id}" parent="{$group.upid}">	
							<td>
                                {if $canEditGroup}
								<a href="#{'system/group/edit'|app:0}{$group.group_id}">
									{$group.group_name}
								</a>
                                {else}
                                    {$group.group_name}
                                {/if}
							</td>
							<td>{$group.group_refid}</td>
                            <td>{$group.level}</td>
                            <td>{$group.coins}</td>
                            <td>{$group.rank}</td>
							<td class="hidden-xs hidden-sm">{$group.note|escape}</td>
							<td class="text-right">
								<div class="btn-group">
                                    {if $canSetService && $type!='admin'}
                                        <a href="#{'system/service/config'|app:0}{$group.group_id}" class="btn btn-xs btn-primary">
                                            <i class="fa fa-suitcase"></i>
                                        </a>
                                    {/if}
								{if $hasDusergroup}
								<a href="{'system/group/del'|app}{$group.group_id}" 
									class="btn btn-danger btn-xs"
									data-confirm="你真的要删除这个分组吗？"
									target="ajax"><i class="fa fa-trash-o"></i></a>
								{/if}
								</div>
							</td>
						</tr>
                        {foreachelse}
                        <tr>
                            <td colspan="7">
                                无分组
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