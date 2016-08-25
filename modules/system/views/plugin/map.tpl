<div class="panel-body">			  
	<form class="form-horizontal" method="POST" action="{'system/plugin/mapping'|app}" target="ajax" data-confirm="确定要修改吗?">
		<input type="hidden" name="app" value="{$app}"/>
		<fieldset>
			<div class="form-group">												
				<section class="col-sm-12">
					<div class="row">
						<div class="col-sm-12">
							<div class="input-group">						
								<input type="text" class="form-control" name="urlmapping" value="{$urlmapping}"/>
								<div class="input-group-btn">
									<button type="submit" class="btn btn-primary">
										确定
									</button>									
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
		</fieldset>				  		
	</form>
</div>