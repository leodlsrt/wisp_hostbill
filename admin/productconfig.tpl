<tr>
    <td id="getvaluesloader">
        {if $test_connection_result}
            <span style="margin-left: 10px; font-weight: bold;text-transform: capitalize; color: {if $test_connection_result.result == 'Success'}#009900{else}#990000{/if}">
                {$lang.test_configuration}:
                {if $lang[$test_connection_result.result]}{$lang[$test_connection_result.result]}
                {else}{$test_connection_result.result}
                {/if}
                {if $test_connection_result.error}: {$test_connection_result.error}
                {/if}
            </span>
        {/if}
    </td>
</tr>
<tr>
    <td >
        <input type="hidden"/>
        {literal}
            <script type="text/javascript">
                $(function () {
                    $('#tabbedmenu').TabbedMenu({elem: '.tab_content', picker: 'li.ttpicker', aclass: 'active'});
                });
            </script>
        {/literal}
        <ul class="breadcrumb-nav " id="tabbedmenu" style="margin-top:10px;">
            <li class="ttpicker"><a onclick="$('li.ttpicker a').removeClass('active');$(this).addClass('active');return false;" href="#tab_resources" class="active disabled">Resource Limits</a></li>
            <li class="ttpicker"><a onclick="$('li.ttpicker a').removeClass('active');$(this).addClass('active');return false;" href="#tab_nest" class="disabled">Nest Configuration</a></li>
        </ul>
        <div class="tab_container" style="background: #fff">
            <div class="tab_content" id="tab_resources">
                {include file="`$module_templates`config.tpl" ptero_tab='resources'}
            </div>
            <div class="tab_content" id="tab_nest">
                {include file="`$module_templates`config.tpl" ptero_tab='nest'}
            </div>
        </div>
    </td>
</tr>