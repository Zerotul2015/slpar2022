import apiCompare from '../../common/apiCompare'

// initial state
const state = () => ({
    compareProducts: {},
    compareProductsGroupByCategory:{},
})

// getters
const getters = {
    products(state) {
        return state.compareProducts;
    },
    productsGroupByCategory(state){
        return  state.compareProductsGroupByCategory;
    },
    count(state) {
        return Object.keys(state.compareProducts).length;
    }
}

// actions
const actions = {
    getCompare({commit}) {
        apiCompare.compareAct('getCompare', {})
            .then(r => {
                if (r.result === true) {
                    commit('setCompareProducts', r.returnData['products']);
                }
            })
            .catch()
    },
    getCompareWithCategories({commit}) {
        apiCompare.compareAct('getCompare', {'withCategories':true})
            .then(r => {
                if (r.result === true) {
                    commit('setCompareProductsGroupByCategory', r.returnData['products']);
                }
            })
            .catch()
    },
    addProduct({commit}, productId) {
        let sendData = {
            'productId': productId,
        };
        apiCompare.compareAct('addProduct', sendData)
            .then(r => {
                if (r.result === true) {
                    commit('setCompareProducts', r.returnData['products']);
                }
            })
            .catch()
    },
    removeProduct({commit}, productId) {
        let sendData = {
            'productId': productId,
        };
        apiCompare.compareAct('removeProduct', sendData)
            .then(r => {
                if (r.result === true) {
                    commit('setCompareProducts', r.returnData['products']);
                }
            })
            .catch()
    },
    deleteCompare({commit}) {
        apiCompare.compareAct('delCompare', {})
            .then(r => {
                if (r.result === true) {
                    commit('setCompareProducts', {});
                }
            })
            .catch()
    },
}

// mutations
const mutations = {
    setCompareProductsGroupByCategory(state, productsGroupByCategory) {
        state.compareProductsGroupByCategory = productsGroupByCategory
    },
    setCompareProducts(state, compareProducts) {
        state.compareProducts = compareProducts
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}