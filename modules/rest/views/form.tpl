<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-anchor"></i> 接入应用管理
			<span>&gt; 管理应用</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default btn-labeled" href="{'rest/app'|app}" 
				target="tag" data-tag="#content">
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
                id="wid-user-form-1"     
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
	                <h2> 应用编辑器 </h2>
	                <ul class="nav nav-tabs pull-right in">
						<li class="active">
							<a href="#base-rest-info" data-toggle="tab">
								<i class="fa fa-anchor"></i> 
								<span class="hidden-mobile hidden-tablet">基本信息</span>
							</a>
						</li>						
					</ul>                    
                </header>
                <div>
                 <div class="widget-body widget-hide-overflow no-padding">
                      <form class="smart-form tab-content" name="RestAppForm" data-widget="nuiValidate" action="{'rest/app/save'|app}" method="post" id="rest-form" target="ajax">
                      	<input type="hidden" name="id" value="{$id}"/>                      	
						<div id="base-rest-info" class="tab-pane active">
							<fieldset>
								<section>
									<label class="label">应用名</label>
									<label class="input">
									<i class="icon-append fa fa-anchor"></i>
									<input type="text" name="name" id="name" value="{$name}"/>
									</label>
								</section>												
								<div class="row">
									<section class="col col-6">
										<label class="label">应用ID</label>
										<label class="input">
										<i class="icon-append fa fa-anchor"></i>
										<input type="text" name="appkey" id="appkey" readonly="readonly" value="{$appkey}"/>
										</label>
									</section>
									<section class="col col-6">
										<label class="label">应用安全码</label>
										<label class="input">
										<i class="icon-append fa fa-anchor"></i>
										<input type="text" name="appsecret" id="appsecret" value="{$appsecret}"/>
										</label>
									</section>
								</div>
								<section>
									<label class="label">通信接口URL</label>
									<label class="input">
										<i class="icon-append fa fa-anchor"></i>
										<input type="text" name="callback_url" id="callback_url" value="{$callback_url}"/>
									</label>
									<div class="note">如果对方也开启了RESTFul服务器时可以指定。用于服务发现，第三方主动通信等.</div>
								</section>
								<section>
									<label class="label">说明</label>
									<label class="textarea">
									<i class="icon-append fa fa-envelope-o"></i>
									<textarea name="note" 
										id="note">{$note|escape}</textarea>
									</label>
								</section>												
								
							</fieldset>	
						</div>						
						
						<footer>
							<button type="submit" class="btn btn-primary">
								保存
							</button>
						</footer>
					</form>                     
                </div>
           </div>
           </div>
		</article>		
	</div>
</section>
<script type="text/javascript">
	nUI.validateRules['RestAppForm'] = {$rules};
</script>