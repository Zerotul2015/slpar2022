import axios from "axios";

const URL_GET_PATH = '/api/customer/';
const ACTION_PATH = {
    'checkAuth': 'check-auth', // проверка авторизации
    'auth': 'auth', // авторизация
    'getCustomer': 'get-customer', // данные покупателя
    'getWholesaleLevels': 'get-wholesale-levels', // уровни оптовых цен с их свойствами
    'registerRequestWholesale': 'register-request-wholesale', //регистрация оптовика
    'registerCustomer': 'register-customer', //регистрация обычного покупателя
    'exit': 'logout', // выход
    'makeOrder': 'make-order', // оформление заказа
    'checkAlreadyRegistered':'check-already-registered' //проверка на то что с такой почтой и телефоном нет покупателя

}

export default {
    action: async function (typeAction, sendData) {
        return await new Promise(async function (resolve, reject) {
            if (!sendData) {
                sendData = {}
            }
            if (ACTION_PATH[typeAction]) {
                await axios.post(URL_GET_PATH + ACTION_PATH[typeAction], sendData)
                    .then(r => {
                        resolve({
                            'result': (r.data.result && r.data.result === true),
                            'error': r.data.error ? r.data.error : null,
                            'returnData': r.data.returnData ? r.data.returnData : null
                        });
                    })
                    .catch(e => {
                        reject({'result': false, 'error': e})
                    })
            } else {
                reject({'result': false, 'error': 'Переданный typeAction не существует'})
            }
        });
    },

}