import api from '../../common/api'

// initial state
const state = () => ({
    cart: {}
})

// getters
const getters = {

}

// actions
const actions = {
    getCart({commit}) {
        api.getData('cart', {})
            .then(r => {
                if (r.result === true) {
                    commit('setCart', r.returnData);
                }
            })
            .catch()
    },
}

// mutations
const mutations = {
    setCart(state, templateData) {
        state.cart = templateData
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}