
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
        <h1 class="txt-color-blueDark">
            <i class="fa-fw fa fa-sitemap"></i> <span>&gt; 生成淘口令</span>
        </h1>
    </div>
    {*<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">*}
        {*<div class="pull-right margin-top-5 margin-bottom-5">*}
            {*<a class="btn btn-default"*}
               {*href="#{'dashen/robot'|app:0}" id="rtnbtn">*}
                {*<i class="glyphicon glyphicon-circle-arrow-left"></i> 返回*}
            {*</a>*}
        {*</div>*}
    {*</div>*}
</div>
<section id="widget-grid">
    <div class="row">
        <article class="col-sm-12">
            <div class="jarviswidget" id="wid-id-1"
                 data-widget-colorbutton="false" data-widget-editbutton="false"
                 data-widget-togglebutton="false" data-widget-deletebutton="false"
                 data-widget-fullscreenbutton="false"
                 data-widget-custombutton="false" data-widget-collapsed="false"
                 data-widget-sortable="false">
                <header>
					<span class="widget-icon"> <i class="fa fa-edit"></i>
					</span>
                    <h2></h2>
                </header>
                <div>
                    <div class="widget-body widget-hide-overflow no-padding">

                        <form name="DashenForm"
                              class="smart-form" >
                            <fieldset>
                                <div class="row">
                                    <section class="col col-3">
                                        <label class="label">淘口令主题图片</label>
                                        <label class="input">
                                            <input value="" id="tbk_logo"  type="text" placeholder="可不填" >
                                        </label>
                                    </section>
                                    <section class="col col-3">
                                        <label class="label">淘口令主题标题</label>
                                        <label class="input">
                                            <input value="" id="tbk_content"  type="text" placeholder="请填写内容" >
                                        </label>
                                    </section>
                                    <section class="col col-3">
                                        <label class="label">淘口令主题标题链接</label>
                                        <label class="input">
                                            <input value="" id="tbk_url"  placeholder="请填写url" type="text">
                                        </label>
                                    </section>
                                    <section class="col col-3">
                                        <label class="label">淘口令主题标题uid</label>
                                        <label class="input">
                                            <input value="" id="tbk_user_id"  placeholder="请填写user_id" type="text">
                                        </label>
                                    </section>
                                </div>
                               <div class="row" id="token_div" style="display: none">
                                   <section class="col col-8">
                                       <label class="label">淘口令，请保存</label>
                                       <label class="input">
                                           <input value="" id="token"  type="text" >
                                       </label>
                                   </section>
                               </div>
                            </fieldset>

                            <footer>
                                <button type="reset" class="btn btn-default">
                                    重置
                                </button>
                                <button  onclick="save();" class="btn btn-primary">
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
<script>

    function save(type){
        var logo = $('#tbk_logo').val();
        var content = $('#tbk_content').val();
        var turl = $('#tbk_url').val();
        var user_id = $('#tbk_user_id').val();
        //console.log(content);
        if(content==''|| turl==''){
            alert('主题内容和主题url参数不可为空');
            return false;
        }
        $.ajax({
            url:"{'taoke/generate/save'|app}",
            type:"post",
            dataType:"json",
            data:{ logo:logo,content:content,turl:turl,user_id:user_id},
            success:function (data) {
                if(data.status==0){
                    $('#token').val(data.msg);
                    $('#token_div').show();

                }else{
                    alert('淘口令未生成');
                }


            }
        })
    }

</script>