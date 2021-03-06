import axios from "axios";

const URL_GET_PATH = '/api/compare/';
const ACTION_PATH = {
    'getCompare': 'get-compare',
    'addProduct': 'add-product',
    'removeProduct': 'remove-product',
    'deleteCompare': 'del-compare',
}

export default {
    compareAct: async function (typeAction, sendData) {
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