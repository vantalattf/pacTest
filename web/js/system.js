'use strict';

let system = {
    colorGood: '#00AB4E',
    colorError: '#C02C0B',
    colorInfo: '#007DB6',
    error: function (text) { // Вывод сообщения об ошибке
        if (text.length !== 0) {
            Toastify({
                text: text,
                duration: 10000,
                newWindow: true,
                close: false,
                gravity: "top", // `top` or `bottom`
                position: "right", // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                style: {
                    background: this.colorError,
                },
            }).showToast();
        }
    },
    info: function (text) { // Вывод информационного сообщения
        if (text.length !== 0) {
            Toastify({
                text: text,
                duration: 5000,
                newWindow: true,
                close: true,
                gravity: "top", // `top` or `bottom`
                position: "right", // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                style: {
                    background: this.colorInfo,
                },
            }).showToast();
        }
    },
    success: function (text) { // Вывод сообщения об успешном завершении операции
        if (text.length !== 0) {
            Toastify({
                text: text,
                duration: 3000,
                newWindow: true,
                close: true,
                gravity: "top", // `top` or `bottom`
                position: "right", // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                style: {
                    background: this.colorGood,
                },
            }).showToast();
        }
    },

    parseResponse: function (json) { // Парсинг JSON с выводом сообщения об ошибке
        if (json === "") {
            throw '';
        }
        try {
            return $.parseJSON(json);
        } catch (e) {
            this.error(e);
            return {
                result: false,
                content: json
            };
        }
    }
}