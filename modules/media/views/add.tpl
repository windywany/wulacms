<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-list-alt"></i> 媒体库
			<span>&gt; 上传</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default btn-labeled" href="#{'media'|app:0}">
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
			<div class="panel panel-default">
				<div class="panel-body no-padding">
					<form id="media-uploader-form" action="{'media/add'|app}" class="smart-form" target="ajax" method="POST">
						<header> 请添加要上传的文件 </header>
						<fieldset>				
						<section>									
							<div class="m-ajax-uploador">						
								<div class="up-file add-btn" 
								 data-auto="#do-upload-btn" 
								 data-name="images"
								 data-max-file-size="{$maxSize}" 
								 data-extensions="{$exts}"
								 id="upload_adea_xxx" 
								 data-multi-upload="true" data-widget="nuiAjaxUploader" for="#images"></div>
								<b class="clearfix"></b>
							</div>
						</section>
						</fieldset>
						<footer>								
							<a class="btn btn-success" id="do-upload-btn">
								<i class="glyphicon glyphicon-cloud-upload"></i> 上传
							</a>								
							<a class="btn btn-default" href="#{'media'|app:0}">
								返回
							</a>
						</footer>
					</form>
				</div>
			</div>
		</article>
	</div>
</section>
<script type="text/javascript">
	$('#upload_adea_xxx').data('UploadCompleteCallback',function(){
		$('#media-uploader-form').submit();
	});	
</script>