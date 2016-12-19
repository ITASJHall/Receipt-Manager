<div class="colmask" style="margin-left: 5%; margin-right: 5%;">
    <form action="?view" method="post">
        <?php $count = 0; ?>
        <?php foreach($stats as $stat_array){ ?>
            <?php $count++; ?>
            <fieldset style="float: left;width: 30%; overflow: hidden; height: 370px; margin-bottom: 2%; border-bottom: 3px solid black;">
                <h3><?=$stat_array['title']; ?></h3>
                <fieldset style="max-height: 350px; height: 300px; overflow: auto; overflow-y: auto;">
                <?php foreach($stat_array['stats'] as $stat){ ?>
                  <section style="border-bottom: 1px dashed darkblue; ">
                    <h4><?=$stat['title'];?></h4>
                    <?php if($stat_array['formatter'] == '$') { ?>
                        <p><?=$stat_array['formatter']; ?><?=$stat['stat'];?></p>
                    <?php } else { ?>
                        <p><?=$stat['stat'];?> <?=$stat_array['formatter']; ?></p>
                    <?php } ?>
                  </section>
                <?php } ?>
                </fieldset>
            </fieldset>
            <?php if($count == 3){ ?>
                <br style="clear: left;"/>
                <?php $count = 0; ?>
            <?php } ?>
        <?php } ?>
    </form>
</div>