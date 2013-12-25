{include file='header' pageTitle='wcf.acp.tour.step.list'}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.tour.step.list{/lang}</h1>
	{if $tourID}
		<h2><a href="{link controller='TourEdit' object=$tours[$tourID]}{/link}">{$tours[$tourID]->visibleName|language}</a></h2>
	{/if}
</header>

{if $tourID}
	<div class="contentNavigation">
		{pages print=true assign=pagesLinks controller="TourStepList" object=$tours[$tourID] link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
		
		<nav>
			<ul>
				<li id="jumpToTourTop" class="button dropdown">
					<div class="dropdownToggle" data-toggle="jumpToTourTop"><span class="icon icon16 icon-sort"></span> <span>{lang}wcf.acp.tour.step.jumpToTour{/lang}</span></div>
					<ul class="dropdownMenu">
						{foreach from=$tours item=tour}
							<li><a href="{link controller='TourStepList' object=$tour}{/link}">{$tour->visibleName|language}</a></li>
						{/foreach}
					</ul>
				</li>
				<li><a href="{link controller='TourStepAdd' object=$tours[$tourID]}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.tour.step.add{/lang}</span></a></li>
				{event name='contentNavigationButtonsTop'}
			</ul>
		</nav>
	</div>
	
	{if $objects|count}
		<script data-relocate="true" src="{@$__wcf->getPath('wcf')}acp/js/WCF.ACP.Tour{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@$__wcfVersion}"></script>
		<script data-relocate="true">
			//<![CDATA[
			$(function() {
				// setup clipboard and AJAX actions
				var actionObjects = { };
				actionObjects['delete'] = new WCF.Action.Delete('wcf\\data\\tour\\step\\TourStepAction', $('.jsTourStepRow'));
				WCF.Clipboard.init('wcf\\acp\\page\\TourStepListPage', {@$hasMarkedItems}, { 'com.thurnax.wcf.tour.step': actionObjects });
				new WCF.ACP.Tour.ClipboardToggle('wcf\\data\\tour\\step\\TourStepAction', $('.jsTourStepRow'), $('.jsToggleButton'), 'com.thurnax.wcf.tour.step');
				new WCF.ACP.Tour.ClipboardMove('com.thurnax.wcf.tour.step');
				
				// setup sorting
				new WCF.Sortable.List('tourStepList', 'wcf\\data\\tour\\step\\TourStepAction', {$startIndex}, {
					items: 'tr:not(.sortableNoSorting)',
					toleranceElement: '> *',
					update: function() {
						$('.jsTourStepRow .columnID').each(function(i) {
							$(this).html(WCF.String.formatNumeric(i + {$startIndex}));
						});
					}
				}, true);
				
				// setup empty table handler
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
		</script>
		
		<div id="tourStepList" class="tabularBox tabularBoxTitle marginTop sortableListContainer">
			<header>
				<h2>{lang}wcf.acp.tour.step.list{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
			</header>
			
			<table data-type="com.thurnax.wcf.tour.step" class="table jsClipboardContainer">
				<thead>
					<tr>
						<th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
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
							<td class="columnMark"><input type="checkbox" class="jsClipboardItem" data-object-id="{$tourStep->tourStepID}" /></td>
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
		
		<div class="contentNavigation">
			{@$pagesLinks}
			
			<nav>
				<ul>
					<li id="jumpToTourBottom" class="button dropdown">
						<div class="dropdownToggle" data-toggle="jumpToTourBottom"><span class="icon icon16 icon-home"></span> <span>{lang}wcf.acp.tour.step.jumpToTour{/lang}</span></div>
						<ul class="dropdownMenu">
							{foreach from=$tours item=tour}
								<li><a href="{link controller='TourStepList' object=$tour}{/link}">{$tour->visibleName|language}</a></li>
							{/foreach}
						</ul>
					</li>
					
					<li><a href="{link controller='TourStepAdd' object=$tours[$tourID]}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.tour.step.add{/lang}</span></a></li>
					{event name='contentNavigationButtonsBottom'}
				</ul>
			</nav>
			<nav class="jsClipboardEditor" data-types="[ 'com.thurnax.wcf.tour.step' ]"></nav>
		</div>
		
		<div id="tourStepMoveDialog" class="invisible">
			<fieldset>
				<dl>
					<dt>{lang}wcf.acp.tour{/lang}</dt>
					<dd>
						<select id="tourStepMoveTarget">
							{foreach from=$tours item=$tour}
								<option value="{$tour->tourID}">{$tour->visibleName|language}</option>
							{/foreach}
						</select>
					</dd>
				</dl>
			</fieldset>
			
			<div class="formSubmit">
				<button id="tourStepMove" class="buttonPrimary">{lang}wcf.acp.tour.move{/lang}</button>
				<button id="tourStepMoveCancel">{lang}wcf.global.button.cancel{/lang}</button>
			</div>
		</div>
	{else}
		<p class="info">{lang}wcf.global.noItems{/lang}</p>
	{/if}
{else}
	<div class="contentNavigation">
		<nav>
			<ul>
				<li><a href="{link controller='TourStepAdd'}{/link}" class="button"><span class="icon icon16 icon-sort"></span> <span>{lang}wcf.acp.tour.step.add{/lang}</span></a></li>
				{event name='contentNavigationButtonsNoTour'}
			</ul>
		</nav>
	</div>
	
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.acp.tour.step.filter{/lang}</legend>
			{foreach from=$tours item=$tour}
				<dl>
					<dd><a href="{link controller='TourStepList' object=$tour}{/link}">{$tour->visibleName|language}</a></dd>
				</dl>
			{/foreach}
		</fieldset>
	</div>
{/if}

{include file='footer'}
