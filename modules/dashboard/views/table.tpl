<div class="row">
	<div class="col-md-4 hidden-sm">
		<h1 class="txt-color-blueDark">
			{block title}{/block}
		</h1>
	</div>
	<div class="col-sm-12 col-md-8">
		<div class="pull-right margin-top-5 margin-bottom-5">{block toolbar}{/block}</div>
	</div>
</div>
<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12">
			<div class="panel panel-default">
				{block table}{/block}		
			</div>
		</article>
	</div>
	{block js}{/block}
</section>