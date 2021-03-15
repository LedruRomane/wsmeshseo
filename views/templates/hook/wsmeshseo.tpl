{function maillage level=1}
    {assign 'index' 0}
    {foreach from=Category::getChildren($idcategory, $language.id) item=childCat}
        {if $index > 13}
            {break}
        {/if}
        {assign "childCatFull" value=Category::getInstance($childCat.id_category)}
        {if $childCatFull->getWsNbProductsRecursive() > 0}
            {$maillageArray[$level][] = $childCat scope=parent}
            {if $maxlevel != $childCatFull->level_depth}
                {maillage idcategory=$childCat.id_category level=$level+1 maxlevel=$maxlevel}
            {/if}
            {$index = $index + 1}
        {/if}
    {/foreach}
{/function}
{block name="in_wrapper_top"}
{if isset($category)}
        <div id="category-seo" class="container">

                {assign var='currentCat' value=Category::getInstance($category.id)}
                {assign var='parentsCat' value=$currentCat->getParentsCategories($language.id)}
                {* Récupération du type de média de la catégorie courante *}
                {assign var='maillageArray' value=[]}
                {assign var='mediaTypeCat' value=$parentsCat[$parentsCat|@count - 2]}
                {assign var='mediaTypeCatFull' value=Category::getInstance($mediaTypeCat.id_category)}

                {* Récupération des différents type de média *}
                {* {foreach from=Category::getChildren(103, $language.id) item=childCat}
                    {assign "childCatFull" value=Category::getInstance($childCat.id_category)}
                    {if $childCatFull->getWsNbProductsRecursive() > 0}
                        {$maillageArray[0][] = $childCat scope=parent}
                    {/if}
                {/foreach} *}

                {* Récupération de tout les parents avec leur enfants de la catégorie courante*}
                {if $mediaTypeCat.id_category != $category.id_parent && $category.id_parent != 103}
                    {maillage idcategory=$mediaTypeCat.id_category maxlevel=($category.level_depth - 1)}
                {/if}

                {* Récupération des catégorie soeur de la catégorie courante *}
                {assign var='index' value=0}
                {assign var='maillageArrayDepth' value=$maillageArray|@count}
                {if $category.id_parent != 103}
                    {foreach from=Category::getChildren($category.id_parent, $language.id) item=childCat}
                            {if $index > 20}
                                {break}
                            {/if}
                            {assign "childCatFull" value=Category::getInstance($childCat.id_category)}
                            {if $childCatFull->getWsNbProductsRecursive() > 0}
                                {$maillageArray[$maillageArrayDepth+1][] = $childCat scope=parent}
                                {$index = $index + 1}
                            {/if}
                    {/foreach}
                {/if}

                {* Récupération des catégories enfant de la catégorie courante. *}
                {assign var='maillageArrayDepth' value=$maillageArray|@count}
                {maillage idcategory=$category.id maxlevel=99 level=$maillageArrayDepth+1}

                {* Affichage du maillage *}
                {if $maillageArray|@count > 0}
                    {$parents|@var_dump}
                    <h2 class="maillage_header" id="maillage_{$mediaTypeCat.id_category}">
                        {if "maillage_"|cat:$mediaTypeCat.id_category}
                            {"maillage_"|cat:$mediaTypeCat.id_category}
                        {else}
                            {l s="Faire de la publicité "}{$mediaTypeCat.name}
                        {/if}
                    </h2>
                    <div id="category-accordion">
                        <div class="category-accordion-headers">
                            {foreach from=$maillageArray item=typeCategory key=depth name="maillageheader"}
                                <a id="maillage_{$mediaTypeCat.id_category}_{$typeCategory@iteration+1}" class="category-accordion-header {if !$smarty.foreach.maillageheader.first} collapsed {/if}" data-toggle="collapse" href="#cat{$depth}">
                                    {assign var="toTranslate" value="maillage_{$mediaTypeCat.id_category}_{$typeCategory@iteration+1}"}
                                    {if $toTranslate}
                                        {$toTranslate}
                                    {else}
                                        {l s="Catégorie"}
                                    {/if}
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