import apiCustomer from '../../common/apiCustomer'

// initial state
const state = () => ({
    authData: {}, //{isAuth:bool, isWholesale:bool, customerId:null|int}
    customerData: {},
    customerCompany: [],
    wholesaleData: {}, // details[] + levelId
    wholesaleLevelsData: [], //все уровни оптовиков с настройками
    requestRegisterWholesaleSend: null,
    requestRegisterSend: null,
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
        if (state.authData.isAuth) {
            return state.authData.isAuth
        } else {
            return false
        }
    },
    isWholesale(state) {
        if (state.authData.isWholesale) {
            return state.authData.isWholesale
        } else {
            return false
        }
    },
    authData(state) {
        return state.authData;
    },
    discountLevel(state) {
        if (state.wholesaleData.levelId) {
            return state.wholesaleData.levelId;
        } else {
            return null;
        }
    },
    requestRegisterWholesaleSend(state) {
        return state.requestRegisterWholesaleSend;
    },
    requestRegisterSend(state) {
        return state.requestRegisterSend;
    }
}

// actions
const actions = {
    checkAuth({commit}) {
        apiCustomer.action('checkAuth', {})
            .then(r => {
                if (r.result === true) {
                    commit('setAuthData', r.returnData);
                } else {
                    commit('setAuthData', {});
                }
            })
            .catch()
    },
    auth({commit}, formData) {
        if (formData && formData.login && formData.pass) {
            apiCustomer.action('auth', {'login': formData.login, 'pass': formData.pass})
                .then(r => {
                    if (r.result === true) {
                        commit('setAuthData', r.returnData);
                    } else {
                        commit('setAuthData', {});
                    }
                })
                .catch()
        }
    },
    exit({commit}, login, pass) {
        if (login && pass) {
            apiCustomer.action('exit', {})
                .then(r => {
                    if (r.result === true) {
                        commit('setAuthData', {});
                        commit('setCustomerData', {});
                        commit('setWholesaleData', {});
                    }
                })
                .catch()
        }
    },
    getDealerData({commit, state}) {
        if (state.authData.isAuth === true && state.authData.customerId) {
            apiCustomer.action('getCustomer', {})
                .then(r => {
                    if (r.result === true) {
                        if (r.returnData.customer) {
                            commit('setCustomerData', r.returnData.customer);
                        }
                        if (r.returnData.wholesale) {
                            commit('setWholesaleData', r.returnData.wholesale);
                        }
                    }
                })
                .catch()
        }
    },
    getWholesaleLevels({commit, state}) {
        if (state.authData.isAuth === true && state.authData.customerId) {
            apiCustomer.action('getWholesaleLevels', {})
                .then(r => {
                    if (r.result === true) {
                        if (r.returnData) {
                            commit('setWholesaleLevels', r.returnData.customer);
                        }
                    }
                })
                .catch()
        }
    },
    registerRequestWholesale({commit, state}, formData) {
        if (formData) {
            apiCustomer.action('registerRequestWholesale', formData)
                .then(r => {
                    if (r.result === true) {
                        commit('setRequestRegisterWholesaleSend', true);
                    } else {
                        commit('setRequestRegisterWholesaleSend', false);
                    }
                })
                .catch()
        }
    },
    registerRequest({commit, state}, formData) {
        if (formData) {
            apiCustomer.action('registerRequest', formData)
                .then(r => {
                    if (r.result === true) {
                        commit('setRequestRegisterSend', true);
                    } else {
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
    setWholesaleLevels(state, wholesaleLevels) {
        state.wholesaleLevelsData = wholesaleLevels;
    },
    setRequestRegisterWholesaleSend(state, requestRegisterSend) {
        state.requestRegisterWholesaleSend = requestRegisterSend;
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