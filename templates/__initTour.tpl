{if $__wcf->getTourHandler()->isEnabled()}
	WCF.Tour.availableTours = {@$__wcf->getTourHandler()->getAvailableManualTours()|json_encode};
	{if $__wcf->getTourHandler()->getActiveTour()}WCF.Tour.loadTour({$__wcf->getTourHandler()->getActiveTour()}, true);{/if}
{/if}
