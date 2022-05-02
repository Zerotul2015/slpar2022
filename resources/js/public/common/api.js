import axios from "axios";

const URL_GET_PATH = '/api/get-data/';
const DATA_PATH = {
    'page': 'page',
    'pageCategory': 'page-category',
    'productCategory': 'product-category',
    'productManufacturer': 'product-manufacturer',
    'productUnit': 'product-unit',
    'productStockStatus': 'product-stock-status',
    'product': 'product',
    'productRelated': 'product-related',
    'bathStyle':'bath-style',
    'searchSite':'search-site',
    'templateData':'template-data',
    'favorite':'favorite',
    'discount':'discount',
}

export default {
    getData: async function (typeData, sendData) {
        return await new Promise(async function (resolve, reject) {
            if (!sendData) {
                sendData = {}
            }
            if (DATA_PATH[typeData]) {
                await axios.post(URL_GET_PATH + DATA_PATH[typeData], sendData)
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
                reject({'result': false, 'error': 'Переданный typeData не существует'})
            }
        });
    },

}