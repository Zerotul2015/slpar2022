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
    discountsAsText(state) {
        let textArray =[];
        //{"id":3,"name":"От 5000 скидка 5%","type":"sum","conditions":{"minSum":5000},"enable":1,"unit":"percent","amount":5}
        if(state.discounts && state.discounts.length > 0){
            state.discounts.forEach(discount=>{
                let text = '-' + discount.amount;
                if(discount.unit === 'percent'){
                    text = text +'%';
                }
                if(discount.unit === 'rub'){
                    text = text +'р.';
                }
                text = text + ' при покупке ';
                if(discount.type === 'count'){
                    text = text + discount.conditions.minCount + ' товаров';
                }
                if(discount.type === 'sum'){
                    text = text + 'на ' + discount.conditions.minSum + 'р.';
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