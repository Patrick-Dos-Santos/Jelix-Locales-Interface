<!-->
Probleme : 
- il faut virer les charset et utiliser celui par défaut
- remplacer le titre de la page par défaut
- supprimer les méthodes inutiles
<-->
<form action="{jurl 'locales~locales:savecreate'}" method="post">
<table>
    <tr>
        <th></th>
        {foreach $locales as $l}
        <th>{$l}</th>
        {/foreach}
    </tr>
        {foreach $params_name as $id=>$key}
        <tr>
            <td>{$key}</td>
            {foreach $locales as $l}
                        <td>
                            <input type="text" name="{$id}::{$l}" 
                            {foreach $params_value as $param=>$value}
                                {if $param == $id.$l}
                                value="{$value}"
                                {/if}
                            {/foreach}
                            />
                        </td>
            {/foreach}
        </tr>
        {/foreach}
</table>
<input type="submit" />
</form>

