<!-->
Probleme : 
- les espaces et . sont remplacés par des _ lors de la récupération des params
- il faut virer les charset et utiliser celui par défaut
<-->
<form action="{jurl 'locales~locales:savecreate'}" method="post">
<table>
    <tr>
        <th></th>
        {foreach $locales as $l}
        <th>{$l}</th>
        {/foreach}
    </tr>
        {foreach $params_name as $fp=>$id}
        <tr>
            <td>{$fp}</td>
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

