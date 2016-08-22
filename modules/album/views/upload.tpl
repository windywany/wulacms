<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-picture-o"></i> <a href="#{'album/pic'|app:0}{$album_id}">{$album_name}</a>
			<span>&gt; 上传相片</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default" href="#{'album/pic'|app:0}{$album_id}">
				<i class="glyphicon glyphicon-circle-arrow-left"></i> 返回相册
			</a>			
		</div>
	</div>
</div>
<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body no-padding">
					<form id="media-uploader-form" action="{'album/upload'|app}" class="smart-form" target="ajax" method="POST">
						<header> 请添加要上传的相片 </header>
						<fieldset>				
						<input type="hidden" name="album_id" value="{$album_id}"/>
						<section>									
							<div class="m-ajax-uploador">						
								<div class="up-file add-btn" 
								 data-auto="#do-upload-btn" 
								 data-name="images"
								 data-max-file-size="{$maxSize}" 
								 data-extensions="{$exts}"
								 id="upload_album_pic" 
								 data-water="0"
								 data-multi-upload="true" data-widget="nuiAjaxUploader" for="#images"></div>
								<b class="clearfix"></b>
							</div>
						</section>
						</fieldset>
						<footer>								
							<a class="btn btn-success" id="do-upload-btn">
								<i class="glyphicon glyphicon-cloud-upload"></i> 上传
							</a>								
							<a class="btn btn-default" href="#{'album/pic'|app:0}{$album_id}">
								<i class="glyphicon glyphicon-circle-arrow-left"></i> 返回相册
							</a>
						</footer>
					</form>
				</div>
			</div>
		</article>
	</div>
</section>
<script type="text/javascript">
	$('#upload_album_pic').data('UploadCompleteCallback',function(){
		$('#media-uploader-form').submit();
	});	
</script>