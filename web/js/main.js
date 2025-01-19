'use strict';

// Показать фотографию
function showPhoto(img) {
    let url = $(img).prop('src');
    $mediumModalContent.html('<img src="' + url + '" class="width100p">');
    mediumModal.open('#mediumModal');
}

let edit = {
    descriptionForm: function (button) {
        let id = button.dataset.id,
            type = button.dataset.type,
            place = button.dataset.place;
        $.ajax({
            url: '/default/editDescriptionForm',
            method: 'POST',
            datatype: 'json',
            data: {id: id, type: type, place: place},
            success: function (resp) {
                let result = system.parseResponse(resp);
                if (result.result !== true) {
                    system.error(result.content);
                    return;
                }
                $mediumModalContent.html(result.content);
                mediumModal.open('#mediumModal');
            }
        });

    },
    // Сохраняем описание
    descriptionSave: function (button) {
        let $form = $(button).closest('.js-parent-block'),
            id = button.dataset.id,
            type = button.dataset.type,
            description = $form.find('.js-edit-description').val();
        $.ajax({
            url: '/default/saveDescription',
            method: 'POST',
            datatype: 'json',
            data: {id: id, description: description, type: type},
            success: function (resp) {
                let result = system.parseResponse(resp);
                if (result.result !== true) {
                    alert(result.content);
                    return;
                }
                let $place = $('.js-item-' + button.dataset.place).find('.js-' + button.dataset.place + '-place');
                $place.html(result.content);
                mediumModal.close();
            }
        });
    },
}

let image = {
    addImageForm: function () {
        $.ajax({
            url: '/images/addImageForm',
            method: 'POST',
            datatype: 'json',
            success: function (resp) {
                let result = system.parseResponse(resp);
                if (result.result !== true) {
                    system.error(result.content);
                    return;
                }
                $mediumModalContent.html(result.content);
                mediumModal.open('#mediumModal');
            }
        });
    },
    // Добавление изображения
    addImage: function (button) {
        let $form = $(button).closest('.js-parent-block').find('form'),
            formData = new FormData(),
            shipId = $form.find('select[name="shipId"]').val(),
            title = $form.find('input[name="title"]').val(),
            url = $form.find('input[name="url"]').val(),
            file = $form.find('input[name="file"]');
        formData.append('shipId', shipId);
        formData.append('title', title);
        formData.append('url', url);
        if (file[0]) {
            formData.append('file', file[0].files[0]);
        }

        $.ajax({
            url: 'images/addImage',
            method: 'POST',
            datatype: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            success: function (resp) {
                let result = system.parseResponse(resp);
                if (result.result !== true) {
                    alert(result.content);
                    return;
                }
                location.reload();
            }
        });
    },
    // Удаление записи о картинке
    deleteImage: function (button) {
        if (!confirm('Вы уверены, что хотите удалить данное изображение?')) {
            return;
        }
        let id = button.dataset.id,
            $row = $(button).closest('.js-parent-row');
        $.ajax({
            url: '/images/deleteImage',
            method: 'POST',
            datatype: 'json',
            data: {id: id},
            success: function (resp) {
                let result = system.parseResponse(resp);
                if (result.result !== true) {
                    system.error(result.content);
                    return;
                }
                $row.remove();
            }
        });
    }
}