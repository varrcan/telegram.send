/**
 * telegram.send main scripts
 */

var user;

jQuery(document).ready(function() {

  // Выбор select без ctrl
  $('select').mousedown(function(event) {
    event.preventDefault();

    var select = this;
    var scroll = select.scrollTop;
    event.target.selected = !event.target.selected;
    setTimeout(function() {select.scrollTop = scroll;}, 0);
    $('select').focus();
  }).mousemove(function(e) {e.preventDefault();});

  // false для checkbox
  $('.module_on:checkbox').on('change', function() {
    if (this.checked) {
      $(this).val('1');
    } else {
      $(this).val('0');
    }
  });

  // Сохранение настроек
  $('#save').click(function(event) {
    event.preventDefault();
    var btn = this;

    $(btn).fadeTo(0, 0.5);
    $.ajax({
      url: '/bitrix/admin/telegram_main.php',
      type: 'POST',
      dataType: 'json',
      data: {
        funcName: 'saveConfig',
        fields: {
          module_on: $('.module_on').val(),
          name_bot: $('.name_bot').val(),
          token: $('.token').val(),
          mail: $('.mail').val()
        }
      }
    }).done(function(data) {
      if (Object.keys(data['message']).length > 0) {
        $('.telegram-response').html(data['message']).fadeIn(500);
      }
    }).fail(function(data) {
      console.log(data);
      alert('Error. Please, refresh page!');
    }).always(function() {
      $(btn).fadeTo(0, 1);
    });
  });

  // Получение входящих запросов
  $('#updates_user').click(function(event) {
    event.preventDefault();
    var btn = this;

    $(btn).fadeTo(0, 0.5);
    $.ajax({
      type: "POST",
      dataType: 'json',
      url: '/bitrix/admin/telegram_main.php',
      data: {
        funcName: 'getUpdates'
      }
    }).done(function(data) {
      user = data['updates'];

      if (Object.keys(user).length > 0) {
        $('.new_user').html('<td colspan="6">Новый пользователь</td>');

        $('.telegram_user').html(
            '<td id="idUser">' + user['id'] + '</td>' +
            '<td id="nicknameUser">' + user['username'] + '</td>' +
            '<td id="usernameUser">' + user['first_name'] + ' ' + user['last_name'] + '</td>' +
            '<td><input id="save_user" name="save_user" value="Сохранить" type="button" onclick="saveUser();"></td>'
        );
      } else {
        $('.telegram-response').html(data['message']).fadeIn(500);
      }

    }).fail(function(data) {
      console.log(data);
      alert('Error. Please, refresh page!');
    }).always(function() {
      $(btn).fadeTo(0, 1);
    });
  });

  // Переключение табов
  $('#tab-setting').click(function(event) {
    event.preventDefault();
    $(this).addClass('adm-detail-tab-active');
    $('#tab-user').removeClass('adm-detail-tab-active');
    $('#wrap-setting').fadeIn(500);
    $('#wrap-user').removeClass('adm-detail-tab-active').fadeOut(0);
  });
  $('#tab-user').click(function(event) {
    event.preventDefault();
    $(this).addClass('adm-detail-tab-active');
    $('#tab-setting').removeClass('adm-detail-tab-active');
    $('#wrap-user').fadeIn(500);
    $('#wrap-setting').fadeOut(0);
  });

});

function saveUser() {
  $.ajax({
    type: 'post',
    dataType: 'json',
    url: '/bitrix/admin/telegram_main.php',
    data: {
      funcName: 'setUser',
      fields: {
        id: user['id'],
        nickname: user['username'],
        username: user['first_name'] + ' ' + user['last_name']
      }
    }
  }).done(function(data) {
    $('.new_user').hide();
    $('#save_user').hide();

    if (Object.keys(data['message']).length > 0) {
      $('.telegram-response').html(data['message']).fadeIn(500);
    }

  }).fail(function(data) {
    console.log(data);
    alert('Error. Please, refresh page!');
  });
}

function delUser(user_id) {
  $.ajax({
    type: 'post',
    dataType: 'json',
    url: '/bitrix/admin/telegram_main.php',
    data: {
      funcName: 'deleteUser',
      fields: {
        id: user_id
      }
    }
  }).done(function(data) {
    $('#' + user_id).hide();

    if (Object.keys(data['message']).length > 0) {
      $('.telegram-response').html(data['message']).fadeIn(500);
    }

  }).fail(function(data) {
    console.log(data);
    alert('Error. Please, refresh page!');
  });
}