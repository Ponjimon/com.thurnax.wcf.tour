{if MODULE_TOUR && $__wcf->session->getPermission('user.tour.enableTour')}
	WCF.Tour.init('{@$__wcf->getPath('wcf')}');
	WCF.Language.addObject({
		'wcf.tour.step.locales.nextBtn': '{lang}wcf.tour.step.locales.nextBtn{/lang}',
		'wcf.tour.step.locales.prevBtn': '{lang}wcf.tour.step.locales.prevBtn{/lang}',
		'wcf.tour.step.locales.doneBtn': '{lang}wcf.tour.step.locales.doneBtn{/lang}',
		'wcf.tour.step.locales.skipBtn': '{lang}wcf.tour.step.locales.skipBtn{/lang}',
		'wcf.tour.step.locales.closeTooltip': '{lang}wcf.tour.step.locales.closeTooltip{/lang}'
	});
{/if}
