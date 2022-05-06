import axios from "axios";

const URL_GET_PATH = '/admin/api/get-data/';
const URL_CHANGE_PATH = '/admin/api/change-data/';
const DATA_PATH = {
    'page': 'page',
    'pageCategory': 'page-category',
    'productCategory': 'product-category',
    'productManufacturer': 'product-manufacturer',
    'productUnit': 'product-unit',
    'productStockStatus': 'product-stock-status',
    'product': 'product',
    'bathStyle':'bath-style',
    'paymentMethods': 'payment-methods',
    'deliveryMethods': 'delivery-methods',
    'orders': 'orders',
    'ordersStatus': 'orders-status',
    'discount': 'discount',
    'promoCode': 'promo-code',
    'customer': 'customer',
    'customerCompany': 'customer-company',
    'banners': 'banners',
    'galleryCategory': 'gallery-category',
    'layouts': 'layouts',
    'menu': 'menu',
    'settings': 'settings',
    'settingsNotifications': 'settings-notifications',
    'users': 'users',
    'wholesaleCustomer': 'wholesale-customer',
    'wholesaleLevel': 'wholesale-level',
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
    applyData: async function (typeData, action, values, cb) {
        return await new Promise(async function (resolve, reject) {
            let checked = false; //результат проверки входных значений
            if (DATA_PATH[typeData]) {
                if (action === 'delete' || action === 'save') {
                    checked = true;
                }
            }
            if (checked === true) {
                await axios.post(URL_CHANGE_PATH + DATA_PATH[typeData],
                    {'action': action, 'values': values})
                    .then(r => {
                        console.log(r);
                        resolve({
                            'result': (r.data.result && r.data.result === true),
                            'error': r.data.error ? r.data.error : null,
                            'returnData': r.data.returnData ? r.data.returnData : null
                        })
                    })
                    .catch(e => {
                        reject({'result': false, 'error': e})
                    })
            } else {
                cb({'result': false})
            }
        })
    },
    uploadFile: async function (arrayFiles) {
        return await new Promise(async function (resolve, reject) {
            let config = {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            };
            let formData = new FormData();
            arrayFiles.forEach((file, fileKey) => {
                formData.append('temp[]', file);
            });
            axios.post('/admin/uploader', formData, config)
                .then((r) => {
                    resolve({
                        'result': (r.data.result && r.data.result === true),
                        'error': r.data.error ? r.data.error : null,
                        'returnData': r.data.url ? r.data.url : null
                    })
                })
                .catch((e) => {
                    reject({
                        'result': false,
                        'error': e.data.error ? e.data.error : null,
                    })
                })
        });
    },

}