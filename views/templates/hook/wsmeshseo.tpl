
{block name="in_wrapper_top"}
{if isset($category)}
        <div id="category-seo" class="container">

                {assign var='currentCat' value=Category::getInstance($category.id)}
                {assign var='parentsCat' value=$currentCat->getParentsCategories($language.id)}
                {* Récupération du type de média de la catégorie courante *}
                {assign var='maillageArray' value=[]}
                {assign var='mediaTypeCat' value=$parentsCat[$parentsCat|@count - 2]}
                {assign var='mediaTypeCatFull' value=Category::getInstance($mediaTypeCat.id_category)}

                {* Affichage du maillage *}
                {if $data|@count > 0}
                    {$data|@var_dump}
                    <h2 class="maillage_header" id="maillage_{$mediaTypeCat.id_category}">
                        {$mediaTypeCat.name}
                    </h2>
                    <div id="category-accordion">
                        <div class="category-accordion-headers">
                            {foreach from=$data item=category key=key name="maillageheader"}
                                <a id="maillage_{$mediaTypeCat.id_category}_{$typeCategory@iteration+1}" class="category-accordion-header {if !$smarty.foreach.maillageheader.first} collapsed {/if}" data-toggle="collapse" href="#cat{$depth}">
                                    {assign var="toTranslate" value="maillage_{$mediaTypeCat.id_category}_{$typeCategory@iteration+1}"}
                                        {l s="Catégorie"}
                                </a>
                            {/foreach}
                        </div>

                        {foreach from=$maillageArray item=typeCategory key=depth name="maillagelink"}
                            <div id="cat{$depth}" class="collapse {if $smarty.foreach.maillagelink.first} show {/if}" data-parent="#category-accordion">
                                {foreach from=$typeCategory item=category key=index}
                                    <a class="category-accordion-link" href="{$link->getCategoryLink({$category.id_category})}">
                                        {$category.name}
                                    </a>
                                {/foreach}
                            </div>
                        {/foreach}
                    </div>
                {/if}
            {/if}
        </div>

    {literal}
    <script>
        var translation = {
            "maillage_834": "{/literal}{l s="maillage_834"}{literal}",
            "maillage_834_1": "{/literal}{l s="maillage_834_1"}{literal}",
            "maillage_834_2": "{/literal}{l s="maillage_834_2"}{literal}",
            "maillage_834_3": "{/literal}{l s="maillage_834_3"}{literal}",
            "maillage_834_4": "{/literal}{l s="maillage_834_4"}{literal}",
            "maillage_834_5": "{/literal}{l s="maillage_834_5"}{literal}",
            "maillage_837": "{/literal}{l s="maillage_837"}{literal}",
            "maillage_837_1": "{/literal}{l s="maillage_837_1"}{literal}",
            "maillage_837_2": "{/literal}{l s="maillage_837_2"}{literal}",
            "maillage_837_3": "{/literal}{l s="maillage_837_3"}{literal}",
            "maillage_837_4": "{/literal}{l s="maillage_837_4"}{literal}",
            "maillage_837_5": "{/literal}{l s="maillage_837_5"}{literal}",
            "maillage_95": "{/literal}{l s="maillage_95"}{literal}",
            "maillage_95_1": "{/literal}{l s="maillage_95_1"}{literal}",
            "maillage_95_2": "{/literal}{l s="maillage_95_2"}{literal}",
            "maillage_94": "{/literal}{l s="maillage_94"}{literal}",
            "maillage_94_1": "{/literal}{l s="maillage_94_1"}{literal}",
            "maillage_94_2": "{/literal}{l s="maillage_94_2"}{literal}",
            "maillage_94_3": "{/literal}{l s="maillage_94_3"}{literal}",
            "maillage_46": "{/literal}{l s="maillage_46"}{literal}",
            "maillage_46_1": "{/literal}{l s="maillage_46_1"}{literal}",
            "maillage_46_2": "{/literal}{l s="maillage_46_2"}{literal}",
        }
    </script>
    {/literal}
{/block}