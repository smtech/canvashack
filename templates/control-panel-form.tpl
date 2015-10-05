{extends file="form.tpl"}

{block name="form-content"}

	{foreach $hacks as $hack}
	
		<div class="form-group">
			<label class="control-label col-sm-{$formLabelWidth}">{$hack->getName()}</label>
			<div class="col-sm-{12 - $formLabelWidth}">
				<div class="radio">
					<label>
						<input type="radio" name="{$hack->getId()}" value="enable" onchange="this.form.submit();" {if $hack->isEnabled()}checked{/if} />
						Enable
					</label>
					<label>
						<input type="radio" name="{$hack->getId()}" value="disable" onchange="this.form.submit();" {if !$hack->isEnabled()}checked{/if} />
						Disable
					</label>
				</div>
				<p class="help-block">{$hack->getAbstract()}</p>
			</div>
		</div>
					
	{/foreach}

{/block}

{block name="form-buttons"}{/block}