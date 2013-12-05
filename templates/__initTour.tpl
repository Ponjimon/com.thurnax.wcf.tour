{if MODULE_TOUR && $__wcf->session->getPermission('user.tour.enableTour') && $showTour}
	var tour = {
		id: "tour1",
		steps: [
			{assign var="step" value=0}
			{foreach item=tourPoint from=$tourPoints}
				{assign var="step" value=$step+1}
				{
					title: "Testtitel",
					content: "{@$tourPoint->pointText}",
					target: document.querySelector('{$tourPoint->elementName}'),
					placement: "{$tourPoint->position}"
				}{if $step < $tourPointsCount},{/if}
			{/foreach}
		]
	};
	hopscotch.startTour(tour);
{/if}
