import api from '../../common/api'

// initial state
const state = () => ({
    discounts: [],

})

// getters
const getters = {
    discounts(state) {
        return state.discounts;
    },
    //cartSum - сумма корзины без учета промокода и других скидок, кроме скидок на товар.
    //cartCount - к-во товаров в корзине
    discountActive: (state) => (cartSum, cartCount) => {
        let discountActive = null;
        let discountAvailable = state.discounts.filter((discount) => {
            if (discount.type === 'count' && discount.conditions.minCount <= cartCount) {
                return discount;
            }
            if (discount.type === 'sum' && discount.conditions.minSum <= cartSum) {
                return discount;
            }
        });
        if (discountAvailable) {
            let maxDiscount = 0;
            discountAvailable.forEach((discount) => {
                let discountItemVal = 0;
                if (discount.unit === 'percent') {
                    discountItemVal = cartSum / 100 * discount.amount;
                }
                if (discount.unit === 'rub') {
                    discountItemVal = discount.amount;
                }
                if (discountItemVal > maxDiscount) {
                    maxDiscount = discountItemVal;
                    discountActive = discount;
                }
            })
        }
        return discountActive;
    },
    discountAsText: (state) => (id) => {
        let text = '';
        let discountNeed = state.discounts.find(discount => discount.id === id);
        if (discountNeed) {
            text = '-' + discountNeed.amount.toLocaleString('ru');
            if (discountNeed.unit === 'percent') {
                text = text + '%';
            }
            if (discountNeed.unit === 'rub') {
                text = text + ' р.';
            }
            text = text + ' при покупке ';
            if (discountNeed.type === 'count') {
                text = text + discountNeed.conditions.minCount + ' товаров';
            }
            if (discountNeed.type === 'sum') {
                text = text + 'на ' + discountNeed.conditions.minSum.toLocaleString('ru') + ' р.';
            }
        }
        return text;
    },
    discountsAsText(state) {
        let textArray = [];
        //{"id":3,"name":"От 5000 скидка 5%","type":"sum","conditions":{"minSum":5000},"enable":1,"unit":"percent","amount":5}
        if (state.discounts && state.discounts.length > 0) {
            state.discounts.forEach(discount => {
                let text = '-' + discount.amount.toLocaleString('ru');
                if (discount.unit === 'percent') {
                    text = text + '%';
                }
                if (discount.unit === 'rub') {
                    text = text + ' р.';
                }
                text = text + ' при покупке ';
                if (discount.type === 'count') {
                    text = text + discount.conditions.minCount + ' товаров';
                }
                if (discount.type === 'sum') {
                    text = text + 'на ' + discount.conditions.minSum.toLocaleString('ru') + ' р.';
                }
                textArray.push(text);
            });
        }
        return textArray;
    },
}

// actions
const actions = {
    getDiscounts({commit}) {
        let sendData = {
            'where': 'enable',
            'searchString': 1
        }
        api.getData('discount', sendData)
            .then(r => {
                if (r.result === true) {
                    if (r.returnData) {
                        commit('setDiscounts', r.returnData);
                    }
                }
            })
            .catch()
    }
}

// mutations
const mutations = {
    setDiscounts(state, discounts) {
        state.discounts = discounts;
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}