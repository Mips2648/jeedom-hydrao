<?php
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>
<form class="form-horizontal">
    <fieldset>
        <legend><i class="fas fa-user-cog"></i> {{Authentification}}</legend>
        <div class="form-group">
            <label class="col-sm-4 control-label">{{Nom d'utilisateur}}</label>
            <div class="col-sm-4">
                <input type="text" class="configKey form-control" data-l1key="username" placeholder="{{Saisir le nom d'utilisateur}}" />
            </div>
        </div>
        <div class="form-group">

            <label class="col-sm-4 control-label">{{Mot de passe}}</label>
            <div class="col-sm-4">
                <div class="input-group">
                    <input type="text" class="configKey form-control roundedLeft inputPassword" data-l1key="password" autocomplete="off" />
                    <span class=" input-group-btn">
                        <a class="btn btn-default form-control bt_showPass roundedRight"><i class="fas fa-eye"></i></a>
                    </span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">{{Clé API}}</label>
            <div class="col-sm-4">
                <div class="input-group">
                    <input type="text" class="configKey form-control roundedLeft inputPassword" data-l1key="apikey" autocomplete="off" />
                    <span class="input-group-btn">
                        <a class="btn btn-default form-control bt_showPass roundedRight"><i class="fas fa-eye"></i></a>
                    </span>
                </div>
            </div>
        </div>
        <legend><i class="fas fa-sync"></i> {{synchronisation}}</legend>
        <div class="form-group">
            <label class="col-sm-4 control-label">{{Auto-actualisation}}</label>
            <div class="col-sm-2">
                <label class=""><input type="checkbox" class="configKey form-control" data-l1key="autorefresh" /> {{Activer}}</label>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">{{Nombre de douche à synchroniser}}</label>
            <div class="col-sm-2">
                <input type="text" class="configKey form-control" data-l1key="syncLimit" placeholder="500" />
            </div>
        </div>
    </fieldset>
</form>