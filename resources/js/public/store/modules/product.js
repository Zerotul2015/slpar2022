import api from '../../common/api'

// initial state
const state = () => ({
    product: {},
    productsRelated: {},
})

// getters
const getters = {
    product(state) {
        return state.product;
    },
    countImages(state) {
        return state.product.images ? state.product.images.length : 0;
    },
    keyImageMain(state) {
        let indexImage = 0;
        if(state.product.images){
            indexImage = state.product.images.findIndex(item=>item === state.product.image_main);
            if(indexImage === -1){
                indexImage = 0;
            }
        }
        return indexImage;
    },
    productsRelated(state) {
        return state.productsRelated;
    }
}

// actions
const actions = {
    getProductByUrl({commit}, url) {
        let sendData = {
            'where': 'url',
            'searchString': url
        }
        api.getData('product', sendData)
            .then(r => {
                if (r.result === true) {
                    if (r.returnData[0]) {
                        commit('setProduct', r.returnData[0]);
                    }
                }
            })
            .catch()
    },
    getProductsRelated({commit}, url) {
        let sendData = {
            'where': 'url',
            'searchString': url
        }
        api.getData('productRelated', sendData)
            .then(r => {
                if (r.result === true) {
                    if (r.returnData[0]) {
                        commit('setProductsRelated', r.returnData);
                    }
                }
            })
            .catch()
    },
    getProduct({commit}, idProduct) {
        let sendData = {
            'where': 'id',
            'searchString': idProduct
        }
        api.getData('product', sendData)
            .then(r => {
                if (r.result === true) {
                    commit('setProduct', r.returnData);
                }
            })
            .catch()
    },
}

// mutations
const mutations = {
    setProduct(state, product) {
        state.product = product;
    },
    setProductsRelated(state, products) {
        state.productsRelated = products;
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}