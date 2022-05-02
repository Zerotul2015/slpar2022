import apiCart from '../../common/apiCart'

// initial state
const state = () => ({
    cartProducts: {},
    promoCodeUsed: null,
    resultApplyCode: null, //false | true | null
})

// getters
const getters = {
    products(state) {
        return state.cartProducts;
    },
    promoCodeUsed(state) {
        return state.promoCodeUsed;
    },
    count(state) {
        return Object.keys(state.cartProducts).length;
    },
    sum(state) {
        let sum = 0;
        Object.values(state.cartProducts).forEach((cartItem) => {
            sum = (cartItem.product.price * cartItem.count) + sum;
        });
        return sum;
    },
    resultApplyCode(state){
        return state.resultApplyCode;
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
    changeCount({commit}, newVal) {
        let sendData = {
            'productId': newVal[0],
            'count': newVal[1],
        };
        console.log(sendData);
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
                commit('setResultApplyCode', r.result);
                if (r.result === true) {
                    commit('setCartProducts', r.returnData['products']);
                    commit('setPromoCodeUsed', r.returnData['promo_code_used']);
                }else{
                    setTimeout(() => {
                        commit('setResultApplyCode', null);
                    }, 5000);
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
    setResultApplyCode(state, resultApply) {
        state.resultApplyCode = resultApply;
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