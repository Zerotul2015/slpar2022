import api from '../../common/api'

// initial state
const state = () => ({
    product: {}
})

// getters
const getters = {
    product(state){
        return state.product;
    }
}

// actions
const actions = {
    getProductByUrl({commit}, url){
        let sendData = {
            'where': 'url',
            'searchString': url
        }
        api.getData('product', sendData)
            .then(r => {
                if (r.result === true) {
                    commit('setProduct', r.returnData);
                }
            })
            .catch()
    },
    getProduct({commit}, idProduct){
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
    setProduct(state, product){
        state.product = product;
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}