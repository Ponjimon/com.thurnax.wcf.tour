{include file='header' pageTitle='wcf.acp.tour.step.list'}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.tour.step.list{/lang}</h1>
	
	{if $objects|count}<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new WCF.Action.Delete('wcf\\data\\tour\\step\\TourStepAction', $('.jsTourStepRow'));
			new WCF.Action.Toggle('wcf\\data\\tour\\step\\TourStepAction', $('.jsTourStepRow'));
			new WCF.Sortable.List('tourStepList', 'wcf\\data\\tour\\step\\TourStepAction', {$startIndex}, { 
				items: 'tr:not(.sortableNoSorting)', 
				toleranceElement: '> *',
				update: function() {
					$('.jsTourStepRow .columnID').each(function(i) {
						$(this).html(WCF.String.formatNumeric(i + {$startIndex}));
					});
				}
			}, true);
			
			var options = { };
			{if $pages > 1}
				options.refreshPage = true;
				{if $pages == $pageNo}options.updatePageNumber = -1;{/if}
			{else}
				options.emptyMessage = '{lang}wcf.global.noItems{/lang}';
			{/if}
			new WCF.Table.EmptyTableHandler($('#tourStepList'), 'jsTourStepRow', options);
		});
		//]]>
	</script>{/if}
</header>

{if !$tourID}
	<div class="container containerPadding marginTop">
		<fieldset><legend>{lang}wcf.acp.tour.step.filter{/lang}</legend>
			{foreach from=$tours item=$tour}
				<dl>
					<dt>{$tour->tourName}</dt>
					<dd><a href="{link controller='TourStepList' object=$tour}{/link}">{$tour->visibleName|language}</a></dd>
				</dl>
			{/foreach}
		</fieldset>
	</div>
{elseif $objects|count}
	<div class="contentNavigation">
		{pages print=true assign=pagesLinks controller="TourStepList" object=$tours[$tourID] link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
		
		<nav>
			<ul>
				<li><a href="{if $tourID}{link controller='TourStepAdd' object=$tours[$tourID]}{/link}{else}{link controller='TourStepAdd'}{/link}{/if}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.tour.step.add{/lang}</span></a></li>
				{event name='contentNavigationButtonsTop'}
			</ul>
		</nav>
	</div>
	
	<div id="tourStepList" class="tabularBox tabularBoxTitle marginTop sortableListContainer">
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
			
			<tbody class="sortableList simpleSortableList" data-object-id="0">
				{foreach from=$objects item=tourStep}
					<tr class="jsTourStepRow sortableNode" data-object-id="{@$tourStep->tourStepID}">
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
	
	<div class="formSubmit">
		<button data-type="submit">{lang}wcf.global.button.saveSorting{/lang}</button>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
