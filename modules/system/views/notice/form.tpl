<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-notice"></i> 公告管理
			<span>&gt; 发布公司</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default btn-labeled" href="{'system/notice'|app}" 
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
                id="wid-notice-form-1"     
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
	                <h2> 公告编辑器 </h2>
	                <ul class="nav nav-tabs pull-right in">
						<li class="active">
							<a href="#base-notice-info" data-toggle="tab">
								<i class="fa fa-notice"></i> 
								<span class="hidden-mobile hidden-tablet">公告信息</span>
							</a>
						</li>						
					</ul>                    
                </header>
                <div>
                 <div class="widget-body widget-hide-overflow no-padding">
                      <form class="smart-form tab-content" name="SystemNoticeForm" data-widget="nuiValidate" action="{'system/notice/save'|app}" method="post" id="notice-form" target="ajax">
                      	<input type="hidden" name="id" value="{$id}"/>                      	
						<div id="base-notice-info" class="tab-pane active">
							<fieldset>												
								<div class="row">
									<section class="col col-10">
										<label class="label">标题</label>
										<label class="input">										
										<input type="text" name="title" 
											id="title" value="{$title}"/>
										</label>
									</section>
									<section class="col col-2">
										<label class="label">过期时间</label>
										<label class="input">
										<i class="icon-append fa fa-calendar"></i>
										<input type="text" name="expire_time" data-widget="nuiDatepicker" 
											id="expire_time" value="{$expire_time}"/>
										</label>
									</section>
								</div>
								<section>
									<label class="label">公告内容</label>
									<label class="textarea">
									<textarea  name="message" rows="3" id="message">{$message|escape}</textarea>
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
	nUI.validateRules['SystemNoticeForm'] = {$rules};	
</script>