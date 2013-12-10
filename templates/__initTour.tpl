{if MODULE_TOUR && $__wcf->session->getPermission('user.tour.enableTour') && $showTour}
	var tourLocales = {
		nextBtn: "{lang}wcf.tour.point.locales.nextBtn{/lang}",
		prevBtn: "{lang}wcf.tour.point.locales.prevBtn{/lang}",
		doneBtn: "{lang}wcf.tour.point.locales.doneBtn{/lang}",
		skipBtn: "{lang}wcf.tour.point.locales.skipBtn{/lang}",
		closeTooltip: "{lang}wcf.tour.point.locales.closeTooltip{/lang}"
	};
	var tour = {
		id: "tour1",
		i18n: tourLocales,
		steps: [
			{assign var="step" value=0}
			{foreach from=$tourPoints item=tourPoint}
				{assign var="step" value=$step+1}
				{
					title: "Testtitel",
					content: "{@$tourPoint->pointText|language}",
					target: document.querySelector('{@$tourPoint->elementName}'),
					placement: "{$tourPoint->position}"
				}{if $step < $tourPointsCount},{/if}
			{/foreach}
		]
	};
	hopscotch.startTour(tour);
{/if}
