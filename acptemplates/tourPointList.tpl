{include file='header' pageTitle='wcf.acp.tour.tourPoint.list'}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.tour.tourPoint.list{/lang}</h1>
	
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new WCF.Action.Delete('wcf\\data\\tour\\tourPoint\\TourPointAction', $('.jsTourPointRow'));
		});
		//]]>
	</script>
</header>

<div class="contentNavigation">
	{pages print=true assign=pagesLinks controller="TourPointList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
	
	<nav>
		<ul>
			<li><a href="{link controller='TourPointAdd'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.tour.tourPoint.add{/lang}</span></a></li>
			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

{if $objects|count}
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}wcf.acp.tour.tourPoint.list{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
		</header>
		
		<table class="table">
			<thead>
				<tr>
					<th class="columnID columnTourPointID{if $sortField == 'tourPointID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='TourPointList'}pageNo={@$pageNo}&sortField=tourPointID&sortOrder={if $sortField == 'tourPointID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnDigits columnStep{if $sortField == 'step'} active {@$sortOrder}{/if}"><a href="{link controller='TourPointList'}pageNo={@$pageNo}&sortField=step&sortOrder={if $sortField == 'step' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.tour.tourPoint.step{/lang}</a></th>
					<th class="columnElementName{if $sortField == 'elementName'} active {@$sortOrder}{/if}"><a href="{link controller='TourPointList'}pageNo={@$pageNo}&sortField=elementName&sortOrder={if $sortField == 'elementName' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.tour.tourPoint.elementName{/lang}</a></th>
					<th class="columnText{if $sortField == 'pointText'} active {@$sortOrder}{/if}"><a href="{link controller='TourPointList'}pageNo={@$pageNo}&sortField=pointText&sortOrder={if $sortField == 'pointText' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.tour.tourPoint.pointText{/lang}</a></th>
					<th class="columnPosition{if $sortField == 'position'} active {@$sortOrder}{/if}"><a href="{link controller='TourPointList'}pageNo={@$pageNo}&sortField=position&sortOrder={if $sortField == 'position' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.tour.tourPoint.position{/lang}</a></th>
					
					{event name='columnHeads'}
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$objects item=tourPoint}
					<tr class="jsTourPointRow">
						<td class="columnIcon">
							<a href="{link controller='TourPointEdit' id=$tourPoint->tourPointID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
							<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$tourPoint->tourPointID}" data-confirm-message="{lang}wcf.tour.tourPoint.delete.sure{/lang}"></span>
							
							{event name='rowButtons'}
						</td>
						<td class="columnID">{@$tourPoint->tourPointID}</td>
						<td class="columnDigits columnStep"><a href="{link controller='TourPointEdit' id=$tourPoint->tourPointID}{/link}" title="{lang}wcf.acp.tour.tourPoint.edit{/lang}">{#$tourPoint->step}</a></td>
						<td class="columnDigits columnElementName">{$tourPoint->elementName|language|tableWordwrap}</td>
						<td class="columnDigits columnText">{$tourPoint->pointText|language|tableWordwrap}</td>
						<td class="columnDigits columnPosition">{$tourPoint->position|language|tableWordwrap}</td>
						
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
				<li><a href="{link controller='TourPointAdd'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.tour.tourPoint.add{/lang}</span></a></li>
				
				{event name='contentNavigationButtonsBottom'}
			</ul>
		</nav>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
