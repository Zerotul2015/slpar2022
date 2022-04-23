import apiCart from '../../common/apiCart'

// initial state
const state = () => ({
    cartProducts: {},
    promoCodeUsed: null,
})

// getters
const getters = {
    products(state) {
        return state.cartProducts;
    },
    promoCodeUsed(state) {
        return state.promoCodeUsed;
    }
}

// actions
const actions = {
    getCart({commit}) {
        apiCart.cartAction('getCart', {})
            .then(r => {
                if (r.result === true) {
                    commit('setCartProducts', r.returnData['products']);
                    commit('setPromoCodeUsed', r.returnData['promo_code_used']);
                }
            })
            .catch()
    },
    addProduct({commit}, productId, count) {
        let sendData = {
            'productId': productId,
        };
        if (count) {
            sendData['count'] = count;
        }
        apiCart.cartAction('addProduct', sendData)
            .then(r => {
                if (r.result === true) {
                    commit('setCartProducts', r.returnData['products']);
                    commit('setPromoCodeUsed', r.returnData['promo_code_used']);
                }
            })
            .catch()
    },
    removeProduct({commit}, productId) {
        let sendData = {
            'productId': productId,
        };
        apiCart.cartAction('removeProduct', sendData)
            .then(r => {
                if (r.result === true) {
                    commit('setCartProducts', r.returnData['products']);
                    commit('setPromoCodeUsed', r.returnData['promo_code_used']);
                }
            })
            .catch()
    },
    changeCount({commit}, productId, newCount) {
        let sendData = {
            'productId': productId,
            'count': newCount,
        };
        apiCart.cartAction('changeCount', sendData)
            .then(r => {
                if (r.result === true) {
                    commit('setCartProducts', r.returnData['products']);
                    commit('setPromoCodeUsed', r.returnData['promo_code_used']);
                }
            })
            .catch()
    },
    applyPromoCode({commit}, promoCodeText) {
        let sendData = {
            'promoCode': promoCodeText,
        };
        apiCart.cartAction('applyPromoCode', sendData)
            .then(r => {
                if (r.result === true) {
                    commit('setCartProducts', r.returnData['products']);
                    commit('setPromoCodeUsed', r.returnData['promo_code_used']);
                }
            })
            .catch()
    },
    deleteCart({commit}) {
        apiCart.cartAction('deleteCart', {})
            .then(r => {
                if (r.result === true) {
                    commit('setCartProducts', {});
                    commit('setPromoCodeUsed', null);
                }
            })
            .catch()
    },
}

// mutations
const mutations = {
    setCartProducts(state, cartProducts) {
        state.cartProducts = cartProducts;
    },
    setPromoCodeUsed(state, promoCodeUsed) {
        state.promoCodeUsed = promoCodeUsed;
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}