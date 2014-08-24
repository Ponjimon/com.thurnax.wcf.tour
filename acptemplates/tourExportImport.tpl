{include file='header' pageTitle='wcf.acp.tour.exportImportTour'}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.tour.exportImportTour{/lang}</h1>
</header>

{include file='formError'}
{if $success|isset}<p class="success">{lang}wcf.global.success.add{/lang}</p>{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li>
				<a href="{link controller='TourList'}{/link}" class="button">
					<span class="icon icon16 icon-list"></span>
					<span>{lang}wcf.acp.menu.link.user.tour.list{/lang}</span>
				</a>
			</li>

			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{link controller='TourExportImport'}{/link}" enctype="multipart/form-data">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.acp.tour.import{/lang}</legend>

			<dl{if $errorField == 'importSource'} class="formError"{/if}>
				<dt><label for="importSource">{lang}wcf.acp.tour.import.source.upload{/lang}</label></dt>
				<dd>
					<input type="file" id="importSource" name="importSource" value=""/>
					{if $errorField == 'importSource'}
						<small class="innerError">{if $errorType == 'empty'}{lang}wcf.global.form.error.empty{/lang}{else}{lang}wcf.acp.tour.import.source.error.{@$errorType}{/lang}{/if}</small>
					{/if}
					<small>{lang}wcf.acp.tour.import.source.upload.description{/lang}</small>
				</dd>
			</dl>

			{event name='importSourceFields'}
		</fieldset>

		{event name='fieldsets'}
	</div>

	<div class="formSubmit">
		<input type="submit" name="submitButton" value="{lang}wcf.acp.tour.import.button{/lang}" accesskey="s"/>
		<input type="hidden" name="action" value="import"/>
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{if $tours|count}
	<form method="post" action="{link controller='TourExportImport'}{/link}">
		<div class="container containerPadding marginTop">
			<fieldset>
				<legend>{lang}wcf.acp.tour.export{/lang}</legend>

				<dl {if $errorField == 'selectedTours'} class="formError"{/if}>
					<dt>{lang}wcf.acp.tour.export{/lang}</dt>
					<dd>
						{foreach from=$tours item=tour}
							<label><input type="checkbox"
							              name="selectedTours[{$tour->tourID}]"{if $tour->tourID|in_array:$selectedTours} checked="checked"{/if}/> {$tour->visibleName|language}
							</label>
						{/foreach}
						{if $errorField == 'selectedTours'}
							<small class="innerError">{lang}wcf.global.form.error.empty{/lang}</small>
						{/if}
						<small>{lang}wcf.acp.tour.export.description{/lang}</small>
					</dd>
				</dl>

				{event name='exportToursFields'}
			</fieldset>

			{event name='fieldsets'}
		</div>

		<div class="formSubmit">
			<input type="submit" value="{lang}wcf.acp.tour.export.button{/lang}" accesskey="s"/>
			<input type="hidden" name="action" value="export"/>
			{@SECURITY_TOKEN_INPUT_TAG}
		</div>
	</form>
{/if}

{include file='footer'}
