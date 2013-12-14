{include file='header' pageTitle='wcf.acp.tour.step.list'}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.tour.step.list{/lang}</h1>
	
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new WCF.Action.Delete('wcf\\data\\tour\\step\\TourStepAction', $('.jsTourStepRow'));
			new WCF.Action.Toggle('wcf\\data\\tour\\step\\TourStepAction', $('.jsTourStepRow'));

			var options = { };
			{if $pages > 1}
				options.refreshPage = true;
				{if $pages == $pageNo}options.updatePageNumber = -1;{/if}
			{else}
				options.emptyMessage = '{lang}wcf.global.noItems{/lang}';
			{/if}
			new WCF.Table.EmptyTableHandler($('#tourStepListTableContainer'), 'jsTourStepRow', options);
		});
		//]]>
	</script>
</header>

<div class="contentNavigation">
	{pages print=true assign=pagesLinks controller="TourStepList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
	
	<nav>
		<ul>
				<li><a href="{if $tourID}{link controller='TourStepAdd' object=$tours[$tourID]}{/link}{else}{link controller='TourStepAdd'}{/link}{/if}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.tour.step.add{/lang}</span></a></li>
				{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

{if $objects|count}
	<form method="get" action="{link controller='TourStepList'}{/link}">
		<div class="container containerPadding marginTop">
			<fieldset><legend>{lang}wcf.acp.tour.step.filter{/lang}</legend>
				<dl>
					<dt><label for="id">{lang}wcf.acp.tour{/lang}</label></dt>
					<dd>
						<select id="id" name="id">
							<option value="">{lang}wcf.global.noSelection{/lang}</option>
							{foreach from=$tours item=$tour}
								<option value="{$tour->tourID}"{if $tour->tourID == $tourID} selected="selected"{/if}>{$tour->description|language}</option>
							{/foreach}
						</select>
					</dd>
				</dl>
			</fieldset>
		</div>
		
		<div class="formSubmit">
			<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
			{@SID_INPUT_TAG}
		</div>
	</form>
	
	<div id="tourStepListTableContainer" class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}wcf.acp.tour.step.list{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
		</header>
		
		<table class="table">
			<thead>
				<tr>
					<th class="columnDigits{if $sortField == 'showOrder'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='TourStepList'}pageNo={@$pageNo}&sortField=showOrder&sortOrder={if $sortField == 'showOrder' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.tour.step.showOrder{/lang}</a></th>
					<th class="columnText{if $sortField == 'target'} active {@$sortOrder}{/if}"><a href="{link controller='TourStepList'}pageNo={@$pageNo}&sortField=target&sortOrder={if $sortField == 'target' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.tour.step.target{/lang}</a></th>
					<th class="columnText{if $sortField == 'title'} active {@$sortOrder}{/if}"><a href="{link controller='TourStepList'}pageNo={@$pageNo}&sortField=title&sortOrder={if $sortField == 'title' && $sortOder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.tour.step.title{/lang}</a></th>
					<th class="columnText{if $sortField == 'content'} active {@$sortOrder}{/if}"><a href="{link controller='TourStepList'}pageNo={@$pageNo}&sortField=content&sortOrder={if $sortField == 'content' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.tour.step.content{/lang}</a></th>
					<th class="columnText{if $sortField == 'placement'} active {@$sortOrder}{/if}"><a href="{link controller='TourStepList'}pageNo={@$pageNo}&sortField=placement&sortOrder={if $sortField == 'placement' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.tour.step.placement{/lang}</a></th>
					
					{event name='columnHeads'}
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$objects item=tourStep}
					<tr class="jsTourStepRow">
						<td class="columnIcon">
							<span class="icon icon16 icon-check{if $tourStep->isDisabled}-empty{/if} jsToggleButton jsTooltip pointer" title="{lang}wcf.global.button.{if $tourStep->isDisabled}enable{else}disable{/if}{/lang}" data-object-id="{$tourStep->tourStepID}" data-disable-message="{lang}wcf.global.button.disable{/lang}" data-enable-message="{lang}wcf.global.button.enable{/lang}"></span>
							<a href="{link controller='TourStepEdit' id=$tourStep->tourStepID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
							<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$tourStep->tourStepID}" data-confirm-message="{lang}wcf.acp.tour.step.delete.sure{/lang}"></span>
							
							{event name='rowButtons'}
						</td>
						<td class="columnID">{#$tourStep->showOrder}</td>
						<td class="columnText">{$tourStep->target|tableWordwrap}</td>
						<td class="columnText">{if $tourStep->title}{$tourStep->title|language|tableWordwrap}{else}{lang}wcf.acp.tour.step.title.none{/lang}{/if}</td>
						<td class="columnText">{$tourStep->content|language|tableWordwrap}</td>
						<td class="columnText">{lang}wcf.acp.tour.step.placement.{$tourStep->placement}{/lang}</td>
						
						{event name='columns'}
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
	
	<div class="contentNavigation">
		{@$pagesLinks}
		
		<nav>
			<ul>
				<li><a href="{if $tourID}{link controller='TourStepAdd' object=$tours[$tourID]}{/link}{else}{link controller='TourStepAdd'}{/link}{/if}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.tour.step.add{/lang}</span></a></li>
				{event name='contentNavigationButtonsBottom'}
			</ul>
		</nav>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
