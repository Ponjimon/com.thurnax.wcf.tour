{include file='documentHeader'}

<head>
	<title>{lang}wcf.tour.list{/lang} - {lang}wcf.user.usercp{/lang} - {PAGE_TITLE|language}</title>

	{include file='headInclude'}
	<script data-relocate="true">
		new WCF.Tour.RestartTour();
	</script>
</head>

<body id="tpl{$templateName|ucfirst}">
	{include file='userMenuSidebar'}
	{include file='header' sidebarOrientation='left'}

	<header class="boxHeadline">
		<h1>{lang}wcf.tour.list{/lang}</h1>
	</header>

	{include file='userNotice'}
	<p class="info">{lang}wcf.tour.list.notice{/lang}</p>

	{if $tours|count}
		<ul class="container containerList marginTop">
			{foreach from=$tours item=tour}
				<li class="box16">
					<div class="containerHeadline">
						<h3><a class="icon icon16 icon-play" onclick="WCF.Tour.loadTour({$tour->tourID}, true);"></a> {$tour->visibleName|language}
						</h3>
					</div>
				</li>
			{/foreach}
		</ul>
	{else}
		<p class="info">{lang}wcf.tour.list.none{/lang}</p>
	{/if}

	{include file='footer'}

</body>
</html>
