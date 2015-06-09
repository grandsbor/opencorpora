{* Smarty *}
{extends file='common.tpl'}
{block name=content}
<h1>Отмодерированные примеры, которые не изменили корпус</h1>
Общая статистика:
<ul>
<li>Всего: {count($data.samples)}
<li>Опечатка: {$data.total[$smarty.const.MA_SAMPLES_STATUS_MISPRINT]}</li>
<li>Неснимаемая омонимия: {$data.total[$smarty.const.MA_SAMPLES_STATUS_HOMONYMOUS]}</li>
<li>Ручная правка: {$data.total[$smarty.const.MA_SAMPLES_STATUS_MANUAL_EDIT]}</li>
<li>???: {$data.total[-1]|default:0}</li>
</ul>
<table class='table'>
<thead>
    <tr>
        <th>#</th>
        <th>Название</th>
        <th>Ревизия</th>
        <th>Статус</th>
        <th></th>
        <th>Комментарий<br/>(можно редактировать)</th>
    </tr>
    <tr>
        <col></col>
        <col></col>
        <col></col>
        <col></col>
        <col></col>
        <col width="20%"></col>
    </tr>
</thead>
{foreach from=$data.samples item=sample}
<tr>
    <td>{$sample.id}</td>
    <td>{$sample.pool_name}</td>
    <td>{strip}
        {if     $sample.mod_status == $smarty.const.MA_SAMPLES_STATUS_MISPRINT}опечатка
        {elseif $sample.mod_status == $smarty.const.MA_SAMPLES_STATUS_HOMONYMOUS}неснимаемая омонимия
        {elseif $sample.mod_status == $smarty.const.MA_SAMPLES_STATUS_MANUAL_EDIT}<a href="{$web_prefix}/diff.php?rev_id={$sample.revision}">ручная правка</a>
        {else}???
        {/if}
        {/strip}
    </td>
    <td><input type="checkbox" class="approve-sample" data-id="{$sample.id}"/></td>
    <td class="comment-cell" data-id="{$sample.id}" contenteditable>{$sample.comment}</td>
</tr>
{/foreach}
</table>
<script src="{$web_prefix}/assets/js/merge_fails.js"></script>
{/block}
