
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

/*
 * Fonction pour l'ajout de commande, appellé automatiquement par plugin.template
 */
function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = { configuration: {} };
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';

    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';

    tr += '<div class="input-group">';
    tr += '<input class="cmdAttr form-control input-sm roundedLeft" data-l1key="name" placeholder="{{Nom de la commande}}">';
    tr += '<span class="input-group-btn"><a class="cmdAction btn btn-sm btn-default" data-l1key="chooseIcon" title="{{Choisir une icône}}"><i class="fas fa-icons"></i></a></span>';
    tr += '<span class="cmdAttr input-group-addon roundedRight" data-l1key="display" data-l2key="icon" style="font-size:19px;padding:0 5px 0 0!important;"></span>';
    tr += '</div>';
    tr += '</td>';

    tr += '<td>';
    tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="' + init(_cmd.type) + '" disabled style="width : 120px;margin-top : 5px;" />';
    tr += '<input class="cmdAttr form-control type input-sm" data-l1key="subType" value="' + init(_cmd.subType) + '" disabled style="width : 120px;margin-top : 5px;" />';
    tr += '</td>';

    tr += '<td>';
    tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/>{{Afficher}}</label> ';
    tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isHistorized" checked/>{{Historiser}}</label> ';
    tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="display" data-l2key="invertBinary"/>{{Inverser}}</label> ';
    tr += '<div style="margin-top:7px;">';
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">';
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">';
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="unite" placeholder="Unité" title="{{Unité}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">';
    tr += '</div>';
    tr += '</td>';

    if (typeof jeeFrontEnd !== 'undefined' && jeeFrontEnd.jeedomVersion !== 'undefined') {
        tr += '<td>';
        tr += '<span class="cmdAttr" data-l1key="htmlstate"></span>';
        tr += '</td>';
    }

    tr += '<td>';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove" title="{{Supprimer la commande}}"></i>';
    tr += '</td>';
    tr += '</tr>';

    $('#table_cmd tbody').append(tr);
    var tr = $('#table_cmd tbody tr:last');
    jeedom.eqLogic.builSelectCmd({
        id: $('.eqLogicAttr[data-l1key=id]').value(),
        filter: { type: 'info' },
        error: function (error) {
            $('#div_alert').showAlert({ message: error.message, level: 'danger' });
        },
        success: function (result) {
            tr.find('.cmdAttr[data-l1key=value]').append(result);
        }
    });
    tr.setValues(_cmd, '.cmdAttr');
    jeedom.cmd.changeType(tr, init(_cmd.subType));
}

$("#table_cmd").sortable({ axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true });

function printEqLogic(_eqLogic) {
    $('#img_device').attr("src", 'plugins/hydrao/plugin_info/hydrao_icon.png');

    $.ajax({
        type: "POST",
        url: "plugins/hydrao/core/ajax/hydrao.ajax.php",
        data: {
            action: "getImage",
            id: $('.eqLogicAttr[data-l1key=id]').value()
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            if (data.state != 'ok') {
                return;
            }
            $('#img_device').attr("src", data.result);
        }
    });

    if (_eqLogic.configuration.type == 'showerHead') {
        $('.hydrao-shower').show();
    } else {
        $('.hydrao-shower').hide();
    }
}

$('.pluginAction[data-action=openLocation]').on('click', function () {
    window.open($(this).attr("data-location"), "_blank", null);
});

$('#bt_synchydrao').on('click', function () {
    $('#div_alert').hide();
    $.ajax({
        type: "POST",
        url: "plugins/hydrao/core/ajax/hydrao.ajax.php",
        data: {
            action: "syncDevices",
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({ message: data.result, level: 'danger' });
                return;
            }
            $('#div_alert').showAlert({ message: '{{Synchronisation réussie.}}', level: 'success' });
        }
    });
});

$('body').off('hydrao::newDevice').on('hydrao::newDevice', function (_event, _options) {
    if (modifyWithoutSave) {
        $('#div_alert').showAlert({ message: '{{Un nouveau pommeau a été synchronisé. Veuillez réactualiser la page}}', level: 'warning' });
    } else {
        $('#div_alert').showAlert({ message: '{{Un nouveau pommeau a été synchronisé. Actualisation de la page dans 3s...}}', level: 'warning' });
        setTimeout(function () {
            window.location.replace("index.php?v=d&m=hydrao&p=hydrao");
        }, 3000);
    }
});

$('#bt_createCommands').on('click', function () {
    $.ajax({
        type: "POST",
        url: "plugins/hydrao/core/ajax/hydrao.ajax.php",
        data: {
            action: "createCommands",
            id: $('.eqLogicAttr[data-l1key=id]').value()
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({ message: data.result, level: 'danger' });
                return;
            }
            $('#div_alert').showAlert({ message: '{{Opération réalisée avec succès}}', level: 'success' });
            $('.eqLogicDisplayCard[data-eqLogic_id=' + $('.eqLogicAttr[data-l1key=id]').value() + ']').click();
        }
    });
});