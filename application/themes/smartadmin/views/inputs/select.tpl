{function select_options items=[] selected=''}
    {if is_array($items)}
        {foreach $items as $id=>$childrent}
            {if is_string($childrent)}
                <option value="{$id}" {if in_array($id,$selected)}selected{/if} >{$childrent}</option>
            {elseif is_array($childrent)}
                <optgroup label="{$id}">
                   {select_options items=$childrent selected=$selected}
                </optgroup>
            {/if}
        {/foreach}
    {/if}

{/function}

<section class="{if isset($col)}{$col}{/if}">
    <label class="select {$state}">
        {if isset($icon)}
            <i class="icon-prepend {$icon}"></i>
        {/if}
        <select name="{$name}" class="select2">
            <option value="0" > -- No Value --</option>
            {if isset($options) && $options|@count > 0 }
                {select_options items=$options selected=$value}
            {/if}
        </select>
        <i class="icon"></i>
    </label>
</section>