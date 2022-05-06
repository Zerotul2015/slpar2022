import apiOrders from '../../common/apiOrders'

// initial state
const state = () => ({
    orders: [],
    orderId: null,
    pin: null,
    customerMail: null,
    token: null,
    resultMakingOrder:null,
})

// getters
const getters = {
    resultMakingOrder(state) {
        return state.resultMakingOrder;
    },
    orders(state) {
        return state.orders;
    },
    pin(state) {
        return state.pin;
    },
    token(state) {
        return state.token;
    },
}

// actions
const actions = {
    makingOrder({commit, dispatch}, ordersDetails){
        let sendData = {
            'ordersDetails': ordersDetails,
        };
        apiOrders.cartAction('makingOrder', sendData)
            .then(r => {
                if (r.result === true) {
                    dispatch('cart/getCart', null, {root:true})
                    commit('setResultMakingOrder', true);
                }else{
                    commit('setResultMakingOrder', false);
                }
                setTimeout(()=>{
                    commit('setResultMakingOrder', null);
                },)
            })
            .catch()
    },
    restorePin({commit}, ordersDetails){
        let sendData = {
            'ordersDetails': ordersDetails,
        };
        apiOrders.cartAction('restoreAccess', sendData)
            .then(r => {
                if (r.result === true) {
                    commit('setOrders', r.returnData);
                }
            })
            .catch()
    },
    getOrders({commit,state}, ordersDetails){
        let sendData = {
            'orderId': state.orderId,
            'customerMail':state.customerMail,
            'pin':state.pin,
            'token':state.token,
        };
        apiOrders.cartAction('getOrders', sendData)
            .then(r => {
                if (r.result === true) {
                    commit('setOrders', r.returnData);
                }
            })
            .catch()
    },
    changePin({commit}, pin){
        if(pin) {
            commit('setPin', pin);
        }else{
            commit('setPin', null);
        }
    }
}

// mutations
const mutations = {
    setResultMakingOrder(state, result) {
        state.resultMakingOrder = result;
    },
    setOrders(state, orders) {
        state.orders = orders;
    },
    setOrderId(state, id) {
        state.orderId = id;
    },
    setCustomerMail(state, customerMail) {
        state.customerMail = customerMail;
    },
    setToken(state, token) {
        state.token = token;
    },
    setPin(state, pin) {
        state.pin = pin;
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}