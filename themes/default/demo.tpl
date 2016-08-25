{$pic_cnt=0}
{cts var=album from=pages title=$tag model='album' channel='pics' sortby='create_time' order='d' limit='0,10' pp='off'}
    {foreach $album.album_items as $pic}
    {if $pic_cnt < 2}
    <a href="{$album|url}"><img src="{$pic.url|media}" alt="{$pic.title}"/></a>
    {/if}
    {$pic_cnt=$pic_cnt+1}
    {/foreach}
{/cts}


