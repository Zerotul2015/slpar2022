import apiFavorite from '../../common/apiFavorite'

// initial state
const state = () => ({
    favoriteProducts: {}
})

// getters
const getters = {
    products(state) {
        return state.favoriteProducts;
    },
}

// actions
const actions = {
    getFavorite({commit}) {
        apiFavorite.act('getFavorite', {})
            .then(r => {
                if (r.result === true) {
                    commit('setFavoriteProducts', r.returnData['products']);
                }
            })
            .catch()
    },
    addProduct({commit}, productId) {
        let sendData = {
            'productId': productId,
        };
        apiFavorite.act('addProduct', sendData)
            .then(r => {
                if (r.result === true) {
                    commit('setFavoriteProducts', r.returnData['products']);
                }
            })
            .catch()
    },
    removeProduct({commit}, productId) {
        let sendData = {
            'productId': productId,
        };
        apiFavorite.act('removeProduct', sendData)
            .then(r => {
                if (r.result === true) {
                    commit('setFavoriteProducts', r.returnData['products']);
                }
            })
            .catch()
    },
    deleteFavorite({commit}) {
        apiFavorite.act('delFavorite', {})
            .then(r => {
                if (r.result === true) {
                    commit('setFavoriteProducts', {});
                }
            })
            .catch()
    },
}

// mutations
const mutations = {
    setFavoriteProducts(state, favoriteProducts) {
        state.favoriteProducts = favoriteProducts
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}