import api from '../../common/api'

// initial state
const state = () => ({
    products: [],
    product: {}
})

// getters
const getters = {
    product(state){
        return state.product;
    },
    products(state){
        return state.products;
    },
}

// actions
const actions = {
    getProductsForCategory({commit}, idCat) {
        let sendData = {
            'where': 'category_id',
            'searchString': idCat
        }
        api.getData('product', sendData)
            .then(r => {
                if (r.result === true) {
                    commit('setProducts', r.returnData);
                }
            })
            .catch()
    },
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
    setProducts(state, products){
        state.products = products;
    },
    setProduct(state, product){
        state.product = product;
    },
    setAll(state, products) {
        state.all = products
    },
    setAllById(state, products) {
        state.allById = {...state.allById, ...products}
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}