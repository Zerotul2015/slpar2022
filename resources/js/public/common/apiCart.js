import axios from "axios";

const URL_GET_PATH = '/api/cart/';
const ACTION_PATH = {
    'getCart': 'get-cart',
    'addProduct': 'add-product',
    'removeProduct': 'remove-product',
    'deleteCart': 'del-cart',
    'changeCount': 'change-count',
    'applyPromoCode': 'apply-promo-code',
}

export default {
    cartAction: async function (typeAction, sendData) {
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