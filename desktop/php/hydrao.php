<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('hydrao');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
    <div class="col-xs-12 eqLogicThumbnailDisplay">
        <legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
        <div class="eqLogicThumbnailContainer">
            <div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
                <i class="fas fa-wrench"></i>
                <br>
                <span>{{Configuration}}</span>
            </div>
            <div class="cursor pluginAction logoSecondary" data-action="openLocation" data-location="<?= $plugin->getDocumentation() ?>">
                <i class="fas fa-book"></i>
                <br>
                <span>{{Documentation}}</span>
            </div>
            <div class="cursor pluginAction logoSecondary" data-action="openLocation" data-location="https://community.jeedom.com/tags/plugin-<?= $plugin->getId() ?>">
                <i class="fas fa-comments"></i>
                <br>
                <span>Community</span>
            </div>
            <div class="cursor logoSecondary" id="bt_synchydrao">
                <i class="fas fa-sync"></i>
                <br>
                <span>{{Synchroniser}}</span>
            </div>
        </div>
        <legend><i class="fas fa-shower"></i> {{Mes pommeaux}}</legend>
        <div class="input-group" style="margin:5px;">
            <input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
            <div class="input-group-btn">
                <a id="bt_resetSearch" class="btn roundedRight" style="width:30px"><i class="fas fa-times"></i></a>
            </div>
        </div>
        <div class="eqLogicThumbnailContainer">
            <?php
            foreach ($eqLogics as $eqLogic) {
                $opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
                echo '<div class="eqLogicDisplayCard cursor ' . $opacity . '" data-eqLogic_id="' . $eqLogic->getId() . '">';
                echo '<img src="' . $eqLogic->getImage() . '"/>';
                echo "<br>";
                echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <div class="col-xs-12 eqLogic" style="display: none;">
        <div class="input-group pull-right" style="display:inline-flex">
            <span class="input-group-btn">
                <a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration avancée}}</span>
                </a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
                </a><a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i><span class="hidden-xs"> {{Supprimer}}</span></a>
            </span>
        </div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
            <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
            <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list"></i> {{Commandes}}</a></li>
        </ul>
        <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
            <div role="tabpanel" class="tab-pane active" id="eqlogictab">
                <form class="form-horizontal">
                    <fieldset>
                        <div class="col-sm-7">
                            <legend><i class="fas fa-wrench"></i> {{Paramètres généraux}}</legend>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Nom de l'équipement}}</label>
                                <div class="col-sm-4">
                                    <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                                    <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Objet parent}}</label>
                                <div class="col-sm-4">
                                    <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                                        <option value="">{{Aucun}}</option>
                                        <?php
                                        $options = '';
                                        foreach ((jeeObject::buildTree(null, false)) as $object) {
                                            $options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
                                        }
                                        echo $options;
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Catégorie}}</label>
                                <div class="col-sm-9">
                                    <?php
                                    foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                                        echo '<label class="checkbox-inline">';
                                        echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                                        echo '</label>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"></label>
                                <div class="col-sm-9">
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" />{{Activer}}</label>
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" />{{Visible}}</label>
                                </div>
                            </div>

                            <legend><i class="fas fa-cogs"></i> {{Paramètres spécifiques}}</legend>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Auto-actualisation}}
                                    <sup><i class="fas fa-question-circle tooltips" title="{{Fréquence de rafraîchissement des commandes infos de l'équipement}}"></i></sup>
                                </label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="autorefresh" placeholder="{{Cliquer sur ? pour afficher l'assistant cron}}">
                                        <span class="input-group-btn">
                                            <a class="btn btn-default cursor jeeHelper roundedRight" data-helper="cron" title="Assistant cron">
                                                <i class="fas fa-question-circle"></i>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <legend><i class="fas fa-info"></i> {{Informations}}</legend>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"></label>
                                <div class="col-sm-8">
                                    <a id="bt_createCommands" class="btn btn-default"><i class="fas fa-search"></i> {{Recréer les commandes manquantes}}</a>
                                </div>
                            </div>
                            <div class="form-group hydrao-shower">
                                <label class="col-sm-4 control-label">{{Type}}</label>
                                <div class="col-sm-8">
                                    <span class="label label-default eqLogicAttr" data-l1key="configuration" data-l2key="shower_type"></span>
                                </div>
                            </div>
                            <div class="form-group hydrao-shower">
                                <label class="col-sm-4 control-label"></label>
                                <div class="col-sm-8">
                                    <span class="label label-default eqLogicAttr" data-l1key="configuration" data-l2key="model_type"></span>
                                    <img src="plugins/hydrao/plugin_info/hydrao_icon.png" data-original=".png" id="img_device" class="img-responsive" style="max-height : 240px;margin-top: 10px" />
                                </div>
                            </div>
                            <div class="form-group hydrao-shower">
                                <label class="col-sm-4 control-label">{{Adresse MAC}}</label>
                                <div class="col-sm-8">
                                    <span class="label label-default eqLogicAttr" data-l1key="configuration" data-l2key="mac_address"></span>
                                </div>
                            </div>
                            <div class="form-group hydrao-shower">
                                <label class="col-sm-4 control-label">{{Version hardware}}</label>
                                <div class="col-sm-8">
                                    <span class="label label-default eqLogicAttr" data-l1key="configuration" data-l2key="hw_version"></span>
                                </div>
                            </div>
                            <div class="form-group hydrao-shower">
                                <label class="col-sm-4 control-label">{{Version firmware}}</label>
                                <div class="col-sm-8">
                                    <span class="label label-default eqLogicAttr" data-l1key="configuration" data-l2key="fw_version"></span>
                                </div>
                            </div>

                        </div>
                    </fieldset>
                </form>
            </div>

            <div role="tabpanel" class="tab-pane" id="commandtab">
                <div class="table-responsive">
                    <table id="table_cmd" class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th style="min-width:220px;width:350px;">{{Nom}}</th>
                                <th style="min-width:140px;width:200px;">{{Type}}</th>
                                <th style="min-width:260px;">{{Options}}</th>
                                <?php
                                if (version_compare(jeedom::version(), '4.3.0', '>=')) {
                                ?>
                                    <th>{{Etat}}</th>
                                <?php
                                }
                                ?>
                                <th style="min-width:80px;width:140px;">{{Actions}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_file('desktop', 'hydrao', 'js', 'hydrao'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>