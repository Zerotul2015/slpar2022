import axios from "axios";

const URL_PATH = '/admin/api/export/';
const DATA_PATH = {
    'createYml': 'create-yml',
    'createXlsx': 'create-xlsx',
    'getYml': 'get-yml',
    'getXlsx': 'get-xlsx',
}

export default {
    send: async function (typeData, sendData) {
        return await new Promise(async function (resolve, reject) {
            if (!sendData) {
                sendData = {}
            }
            if (DATA_PATH[typeData]) {
                await axios.post(URL_PATH + DATA_PATH[typeData], sendData)
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