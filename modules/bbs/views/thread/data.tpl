<tbody data-total="{$total}">
{foreach $rows as $row}
<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
</tr>
    {foreachelse}
    <tr>
        <td colspan="5" class="text-align-center">无主题</td>
    </tr>
{/foreach}
</tbody>