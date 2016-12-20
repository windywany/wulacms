<div class="panel-body no-padding">
    <form class="smart-form" action="{'system/column/save'|app}" target="ajax" id="table-column-form" method="post">
        <input type="hidden" name="table" value="{$table}"/>
        <fieldset>

        {foreach $columns as $cid => $col}
            <div class="row">
            <section class="col col-xs-10">
            <label class="toggle">
                <input type="checkbox" name="cols[{$cid}]" value="1" {if $col.show}checked="checked"{/if}/>
                <i data-swchon-text="显示" data-swchoff-text="隐藏"></i>{$col.name}
            </label>
            </section>
                <section class="col col-xs-2">
                    <label class="input">
                        <input type="text" class="input-xs" name="ord[{$cid}]" value="{$col.order|default:99}">
                    </label>
                </section>
            </div>
        {/foreach}

        </fieldset>
    </form>
</div>
<div class="panel-footer">
    <div class="row">
        <div class="col col-sm-12 text-right">
            <a class="btn btn-xs btn-success" onclick="$('#table-column-form').submit()">确定</a>
        </div>
    </div>
</div>