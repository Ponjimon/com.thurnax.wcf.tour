{if $__wcf->getTourHandler()->isEnabled() && $__wcf->getTourHandler()->getActiveTours()}
	WCF.Tour.activeTours = {@$__wcf->getTourHandler()->getActiveTours()|json_encode};
	WCF.Tour.startNextTour();
{/if}
