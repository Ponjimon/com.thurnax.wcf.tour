{if $title}
	<div class="containerHeadline"><h3>{@$title}</h3></div>
{/if}
{@$content}

<ul class="buttonGroup">
	<li><a class="button"><span class="icon icon16 icon-arrow-left"></span> <span>{lang}wcf.tour.step.locales.prevBtn{/lang}</span></a></li>
	{* @todo add additional buttons *}
	<li><a class="button buttonRight"><span>{lang}wcf.tour.step.locales.nextBtn{/lang}</span> <span class="icon icon16 icon-arrow-right"></span></a></li>
</ul>
