<table border="0" width="100%" class="table table-striped" cellpadding="5" cellspacing="0">
    {foreach from=$options item=conf key=k name=checker}
        {if $ptero_tab == $conf._tab}
            <tr id="{$k}row {$ptero_tab}">
                {assign var="name" value=$conf.name}
                {assign var="amodulename" value=$modulename}
                {assign var="baz" value="$amodulename$name"}
                <td>
                    <strong>
                        {if $lang.$baz}{$lang.$baz}
                        {elseif $lang.$name}{$lang.$name}
                        {elseif $name}{$name}
                        {else}{$k}{/if}
                        :</strong>
                    {if $conf.description}
                        <a class="vtip_description" title="{$conf.description}" {if $conf.variable}id="config_{$conf.variable}_descr"{/if}></a>
                        <br/>
                    {/if}
                </td>
                <td {if $conf.variable}id="config_{$conf.variable}"{/if}>
                    {if $conf.type=='input'}
                        <input name="options[{$k}]" value="{if $default.$k!==false}{$default.$k}{elseif $conf.default}{$conf.default}{/if}"/>
                    {elseif $conf.type=='loadable'}
                        {if $k == 'Egg' && !$default.Nest}
                            <i>Please select Nest first & save changes!</i>
                        {else}
                            {if is_array($conf.default) && !empty($conf.default)}
                                <select id="{$k}" name="options[{$k}]" {if $conf.reload}onchange="return getFieldValues('{if $product_id}{$product_id}{else}{$product.id}{/if}')"{/if}>
                                    {foreach from=$conf.default item=cs}
                                        {if $cs|is_array}
                                            <option {if $default.$k== $cs[0]}selected="selected" {/if}value="{$cs[0]}">{$cs[1]}</option>
                                        {else}
                                            <option {if $default.$k== $cs}selected="selected" {/if}>{$cs}</option>
                                        {/if}
                                    {/foreach}
                                </select>
                            {else}
                                <input name="options[{$k}]" value="{if $default.$k}{$default.$k}{/if}"/>
                            {/if}
                        {/if}
                    {elseif $conf.type=='check'}
                        <input type="checkbox" value="1" name="options[{$k}]" {if $default.$k=='1' || (!$default.$k && $conf.default)}checked='checked'{/if}  {if $conf.reload}onchange="return getFieldValues({if $product_id}{$product_id}{else}{$product.id}{/if})"{/if} />
                    {elseif $conf.type=='textarea'}
                        <textarea name="options[{$k}]" rows="5" cols="60" style="margin:0px">{if $default.$k}{$default.$k}{/if}</textarea>
                    {elseif $conf.type=='select'}
                        <div>
                            <select id="conf_opt_{$k}" name="options[{$k}]"
                                    {if $conf.reload}onchange="return getFieldValues({if $product_id}{$product_id}{else}{$product.id}{/if})"
                                    {elseif $conf.onchange}onchange="{$conf.onchange}"{/if}>
                                {foreach from=$conf.default item=cs}
                                    {if $cs|is_array}
                                        <option {if $default.$k== $cs[0]}selected="selected" {/if}value="{$cs[0]}">{$cs[1]}</option>
                                    {else}
                                        <option {if $default.$k== $cs}selected="selected" {/if}>{$cs}</option>
                                    {/if}
                                {/foreach}
                            </select>
                            {if $conf.onchange}
                                <script type="text/javascript">{literal}function lm{/literal}{$k}{literal}() {
                                        $('#conf_opt_{/literal}{$k}{literal}').change();
                                    }
                                    {/literal}lm{$k}();
                                    appendLoader('lm{$k}');
                                </script>
                            {/if}
                        </div>
                    {/if}
                    {if $conf.variable || $conf.forms}
                        <span class="fs11">
                        <input type="checkbox" class="formchecker" rel="{if $conf.variable}{$conf.variable}{else}{$k}{/if}"/>
                        Allow client to adjust during order
                    </span>
                    {/if}
                </td>
            </tr>
        {/if}
    {/foreach}
</table>
{literal}
    <script>
        $(function () {
            $("a.vtip_description").not('.vtip_applied').vTip();
            bindAppWithForms();
        });
    </script>
{/literal}