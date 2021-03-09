<!-- Block ns_monmodule -->
<div id="ns_monmodule_block_home" class="block">
    <h4>{l s='New Slang Link' d='Modules.Ns_MonModule'}</h4>
    <div class="block_content">
        <a href="{$ns_page_link}">
            {if isset($ns_page_name) && $ns_page_name}
                {$ns_page_name}
            {else}
                Votre lien
            {/if}
        </a>
    </div>
</div>
<!-- /Block ns_monmodule -->