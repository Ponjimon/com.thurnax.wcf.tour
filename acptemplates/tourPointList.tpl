{include file='header' pageTitle='wcf.acp.tour.point.list'}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.tour.point.list{/lang}</h1>
	
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new WCF.Action.Delete('wcf\\data\\tour\\point\\TourPointAction', $('.jsTourPointRow'));
		});
		//]]>
	</script>
</header>

<div class="contentNavigation">
	{pages print=true assign=pagesLinks controller="TourPointList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
	
	<nav>
		<ul>
			<li><a href="{link controller='TourPointAdd'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.tour.point.add{/lang}</span></a></li>
			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

{if $objects|count}
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}wcf.acp.tour.point.list{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
		</header>
		
		<table class="table">
			<thead>
				<tr>
					<th class="columnID{if $sortField == 'pointID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='TourPointList'}pageNo={@$pageNo}&sortField=pointID&sortOrder={if $sortField == 'pointID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnDigits{if $sortField == 'step'} active {@$sortOrder}{/if}"><a href="{link controller='TourPointList'}pageNo={@$pageNo}&sortField=step&sortOrder={if $sortField == 'step' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.tour.point.step{/lang}</a></th>
					<th class="columnText{if $sortField == 'elementName'} active {@$sortOrder}{/if}"><a href="{link controller='TourPointList'}pageNo={@$pageNo}&sortField=elementName&sortOrder={if $sortField == 'elementName' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.tour.point.elementName{/lang}</a></th>
					<th class="columnText{if $sortField == 'pointText'} active {@$sortOrder}{/if}"><a href="{link controller='TourPointList'}pageNo={@$pageNo}&sortField=pointText&sortOrder={if $sortField == 'pointText' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.tour.point.pointText{/lang}</a></th>
					<th class="columnText{if $sortField == 'position'} active {@$sortOrder}{/if}"><a href="{link controller='TourPointList'}pageNo={@$pageNo}&sortField=position&sortOrder={if $sortField == 'position' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.tour.point.position{/lang}</a></th>
					
					{event name='columnHeads'}
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$objects item=point}
					<tr class="jsTourPointRow">
						<td class="columnIcon">
							<a href="{link controller='TourPointEdit' id=$point->tourPointID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
							<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$point->pointID}" data-confirm-message="{lang}wcf.acp.tour.point.delete.sure{/lang}"></span>
							
							{event name='rowButtons'}
						</td>
						<td class="columnID">{@$point->pointID}</td>
						<td class="columnDigits"><a href="{link controller='TourPointEdit' id=$point->tourPointID}{/link}" title="{lang}wcf.acp.tour.point.edit{/lang}">{#$point->step}</a></td>
						<td class="columnText">{$point->elementName}</td>
						<td class="columnText">{$point->pointText|language|tableWordwrap}</td>
						<td class="columnText">{lang}wcf.acp.tour.point.position.{$point->position}{/lang}</td>
						
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
				<li><a href="{link controller='TourPointAdd'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.tour.point.add{/lang}</span></a></li>
				
				{event name='contentNavigationButtonsBottom'}
			</ul>
		</nav>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
