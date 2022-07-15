          {if $admin_vars.multilang == 1}
          <div class="langs"><i class="fa fa-language"></i> 
           {foreach from=$langs item=lang key=link}
            {if $currentlang == $link}
                <b><span>{$lang}</span></b>
            {else}
                <a href="?setlang={$link}">{$lang}</a>
            {/if}
           {/foreach}
          </div>
          {/if}
