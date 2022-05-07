import apiDealer from '../../common/apiDealer'

// initial state
const state = () => ({
    authData: {},
    customerData: {},
    wholesaleData: {}, // details[] + levelId
    requestRegisterSend: null
})

// getters
const getters = {
    customer(state) {
        return state.customerData;
    },
    wholesale(state) {
        return state.wholesaleData;
    },
    isAuth(state) {
        if(state.authData.isAuth){
            return state.authData.isAuth
        }else{
            return false
        }
    },
    authData(state) {
        return state.authData;
    },
    discountLevel(state){
        if(state.wholesaleData.levelId){
            return state.wholesaleData.levelId;
        }else{
            return null;
        }
    },
    requestRegisterSend(state) {
        return state.requestRegisterSend;
    }
}

// actions
const actions = {
    checkAuth({commit}) {
        apiDealer.dealerAction('checkAuth', {})
            .then(r => {
                if (r.result === true) {
                    commit('setAuthData', r.returnData);
                }else{
                    commit('setAuthData', {});
                }
            })
            .catch()
    },
    auth({commit}, formData) {
        if(formData && formData.login &&formData.pass) {
            apiDealer.dealerAction('auth', {'login':formData.login, 'pass':formData.pass})
                .then(r => {
                    if (r.result === true) {
                        commit('setAuthData', r.returnData);
                    }else{
                        commit('setAuthData', {});
                    }
                })
                .catch()
        }
    },
    exit({commit}, login, pass) {
        if (login && pass) {
            apiDealer.dealerAction('exit', {})
                .then(r => {
                    if (r.result === true) {
                        commit('setAuthData', {});
                        commit('setCustomerData', {});
                        commit('wholesaleData', {});
                    }
                })
                .catch()
        }
    },
    getDealerData({commit, state}){
        if(state.authData.isAuth === true && state.authData.customerId){
            apiDealer.dealerAction('auth', {})
                .then(r => {
                    if (r.result === true) {
                        if(r.returnData.customer){
                            commit('setCustomerData', r.returnData.customer);
                        }if(r.returnData.wholesale){
                            commit('wholesaleData', r.returnData.wholesale);
                        }
                    }
                })
                .catch()
        }
    },
    registerRequest({commit, state}, formData){
        if(formData){
            apiDealer.dealerAction('registerRequest', formData)
                .then(r => {
                    if (r.result === true) {
                        commit('setRequestRegisterSend', true);
                    }else{
                        commit('setRequestRegisterSend', false);
                    }
                })
                .catch()
        }
    }
}

// mutations
const mutations = {
    setAuthData(state, authData) {
        state.authData = authData;
    },
    setCustomerData(state, customerData) {
        state.customerData = customerData;
    },
    setWholesaleData(state, wholesaleData) {
        state.wholesaleData = wholesaleData;
    },
    setRequestRegisterSend(state, requestRegisterSend) {
        state.requestRegisterSend = requestRegisterSend;
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}