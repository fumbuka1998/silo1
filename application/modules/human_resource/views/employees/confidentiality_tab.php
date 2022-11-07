<?php
/**
 * Created by PhpStorm.
 * User: Bizy Tech
 * Date: 7/8/2019
 * Time: 10:36 AM
 */
?>

<div class="row">
    <div class="col-xs-12">
    </div>
    <div class="col-xs-12">
        <form class="form-inline">
            <div class="col-md-12">
                <div class="col-md-6">
                    <span class="pull-right"><label for="confidentiality_level" >Confidentiality Level :</label></span>&nbsp;&nbsp;
                </div>
                <div class="col-md-6">
                    <select name="confidentiality_level" id="" class="form-control searchable">
                        <?php
                            foreach($confidentiality_levels as $level) { ?>
                                <option value="<?= $level->{$level::DB_TABLE_PK} ?>" <?= (!is_null($user) && $user->confidentiality_level_id == $level->{$level::DB_TABLE_PK}) ? 'selected' : '' ?>><?= $level->level_name ?></option>
                                <?php
                            }
                        ?>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

