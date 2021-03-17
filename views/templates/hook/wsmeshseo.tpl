
{block name="in_wrapper_top"}
    {if isset($data)}
        <div id="category-seo" class="container">

        {assign var='currentCat' value=Category::getInstance($category.id)}
        {assign var='parentsCat' value=$currentCat->getParentsCategories($language.id)}
        {* Récupération du type de média de la catégorie courante *}
        {assign var='maillageArray' value=[]}{assign var='mediaTypeCat' value=$parentsCat[$parentsCat|@count - 2]}
        {assign var='mediaTypeCatFull' value=Category::getInstance($mediaTypeCat.id_category)}

        {* Affichage du maillage *}
        {if $data|@count > 0}
            <h2 class="maillage_header" id="maillage_{$mediaTypeCat.id_category}">
                {$mediaTypeCat.name}
            </h2>
            <div id="accordion">
            {foreach from=$title item=label key=level}
                <div class="card">
                    <div class="card-header" id="heading{$level}">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#collapse{$level}" aria-expanded="true" aria-controls="collapse{$level}">
                                {$label}
                            </button>
                        </h5>
                    </div>
                    <div id="collapse{$level}" class="collapse show" aria-labelledby="heading{$level}" data-parent="#accordion">
                        {foreach from=$data item=categorie key=key}
                            {if $categorie.level_depth == $level}
                                <a class="card-body" href="{$link->getCategoryLink({$categorie.id_category})}">
                                    {$categorie.name}
                                </a>
                            {/if}
                        {/foreach}
                    </div>
                </div>
            {/foreach}
        {/if}
        </div>
    {/if}

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