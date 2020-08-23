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
    <legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
    <div class="eqLogicThumbnailContainer">
        <div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
            <i class="fas fa-wrench"></i>
            <br>
            <span>{{Configuration}}</span>
        </div>
        <div class="cursor pluginAction logoSecondary" data-action="openLocation" data-location="<?=$plugin->getDocumentation()?>">
            <i class="fas fa-book"></i>
            <br>
            <span>{{Documentation}}</span>
        </div>
        <div class="cursor pluginAction logoSecondary" data-action="openLocation" data-location="https://community.jeedom.com/tags/plugin-<?=$plugin->getId()?>">
            <i class="fas fa-comments"></i>
            <br>
            <span>Community</span>
        </div>
        <div class="cursor logoSecondary" id="bt_synchydrao" >
            <i class="fas fa-sync"></i>
            <br>
            <span>{{Synchroniser}}</span>
        </div>
    </div>
    <legend><i class="fas fa-table"></i> {{Mes pommeaux}}</legend>
    <input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
    <div class="eqLogicThumbnailContainer">
        <?php
        foreach ($eqLogics as $eqLogic) {
            $opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
            echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
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
            <a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i> {{Configuration avancée}}</a><a class="btn btn-success btn-sm eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a><a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
        </span>
    </div>
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
        <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
        <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list-alt"></i> {{Commandes}}</a></li>
    </ul>
    <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
        <div role="tabpanel" class="tab-pane active" id="eqlogictab">
            <br/>
            <div class="row">
                <div class="col-sm-7">
                    <form class="form-horizontal">
                        <fieldset>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Nom de l'équipement}}</label>
                                <div class="col-sm-3">
                                    <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                                    <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" >{{Objet parent}}</label>
                                <div class="col-sm-3">
                                    <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                                        <option value="">{{Aucun}}</option>
                                        <?php
                                            foreach (jeeObject::all() as $object) {
                                                echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                                            }
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
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable"/>{{Activer}}</label>
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible"/>{{Visible}}</label>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="col-sm-5">
                    <form class="form-horizontal">
                        <fieldset>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"></label>
                                <div class="col-sm-8">
                                    <a id="bt_createCommands" class="btn btn-default"><i class="fas fa-search"></i> {{Recréer les commandes manquantes}}</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{Type}}</label>
                                <div class="col-sm-8">
                                    <span class="label label-default eqLogicAttr" data-l1key="configuration" data-l2key="type"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{Adresse MAC}}</label>
                                <div class="col-sm-8">
                                    <span class="label label-default eqLogicAttr" data-l1key="configuration" data-l2key="mac_address"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{Version hardware}}</label>
                                <div class="col-sm-8">
                                    <span class="label label-default eqLogicAttr" data-l1key="configuration" data-l2key="hw_version"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{Version firmware}}</label>
                                <div class="col-sm-8">
                                    <span class="label label-default eqLogicAttr" data-l1key="configuration" data-l2key="fw_version"></span>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="commandtab">
            <table id="table_cmd" class="table table-bordered table-condensed">
                <thead>
                    <tr>
                        <th style="width: 400px;">{{Nom}}</th>
                        <th>{{Options}}</th>
                        <th style="width: 150px;">{{Actions}}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_file('desktop', 'hydrao', 'js', 'hydrao');?>
<?php include_file('core', 'plugin.template', 'js');?>
