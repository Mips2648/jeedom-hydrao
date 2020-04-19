
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
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<div class="row">';
    tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="type" style="display : none;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="subType" style="display : none;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="logicalId" style="display : none;">';
    tr += '<div class="col-sm-4">';
    tr += '<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fas fa-flag"></i> Icône</a>';
    tr += '<span class="cmdAttr" data-l1key="display" data-l2key="icon" style="margin-left : 10px;"></span>';
    tr += '</div>';
    tr += '<div class="col-sm-6">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 200px;" placeholder="{{Nom}}">';
    tr += '</div>';
    tr += '</div>';
    tr += '</td>';
    tr += '<td>';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> ';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" />{{Afficher}}</label></span> ';
    tr += '</td>';
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
    tr += '</td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    var tr = $('#table_cmd tbody tr:last');
    jeedom.user.all({
      error: function (error) {
        $('#div_alert').showAlert({message: error.message, level: 'danger'});
      },
      success: function (data) {
        var option = '<option value="">Aucun</option>';
        for (var i in data) {
          option += '<option value="' + data[i].id + '">' + data[i].login + '</option>';
        }
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=user]').empty().append(option);
        tr.setValues(_cmd, '.cmdAttr');
        modifyWithoutSave = false;
      }
    });
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    if (isset(_cmd.type)) {
        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}

$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});

$('.pluginAction[data-action=openLocation]').on('click',function(){
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
            $('#div_alert').showAlert({message: data.result, level: 'danger'});
            return;
        }
        $('#div_alert').showAlert({message: '{{Synchronisation réussie.}}', level: 'success'});
        setTimeout(function() {
            window.location.replace("index.php?v=d&m=hydrao&p=hydrao");
          }, 3000);
        }
    });
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
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            $('#div_alert').showAlert({message: '{{Opération réalisée avec succès}}', level: 'success'});
            $('.eqLogicDisplayCard[data-eqLogic_id=' + $('.eqLogicAttr[data-l1key=id]').value() + ']').click();
        }
    });
});