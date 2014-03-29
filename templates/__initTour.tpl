{if $__wcf->getTourHandler()->isEnabled()}
	WCF.Tour.manualTours = {@$__wcf->getTourHandler()->getManualTours()|json_encode};
	WCF.Tour.availableManualTours = {@$__wcf->getTourHandler()->getAvailableManualTours()|json_encode};
	{if $__wcf->getTourHandler()->getActiveTour()}WCF.Tour.loadTour({$__wcf->getTourHandler()->getActiveTour()}, true);{/if}
{/if}
