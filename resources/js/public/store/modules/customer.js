import apiCustomer from '../../common/apiCustomer'

// initial state
const state = () => ({
    authData: {}, //{isAuth:bool, isWholesale:bool, customerId:null|int}
    customerData: {},
    customerCompany: [],
    wholesaleData: {}, // details[] + levelId
    wholesaleLevelsData: [], //все уровни оптовиков с настройками
    requestRegisterWholesaleResult: {},
    registerResult: {},// {result: false, returnData: "", error: {pass: "Введите пароль"}}
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
    requestRegisterWholesaleResult(state) {
        return state.requestRegisterWholesaleResult.result === undefined ?  null : state.requestRegisterWholesaleResult.result;
    },
    requestRegisterWholesaleError(state) {
        return state.requestRegisterWholesaleResult.error ? state.requestRegisterWholesaleResult.error : null;
    },
    registerResult(state) {
        return state.registerResult.result === undefined ?  null : state.registerResult.result;
    },
    registerErrors(state) {
        return state.registerResult.error ? state.registerResult.error : null;
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
                    commit('setRequestRegisterWholesaleResult', r);
                })
                .catch()
        }
    },
    registerCustomer({commit, state}, formData) {
        if (formData) {
            apiCustomer.action('registerCustomer', formData)
                .then(r => {
                    commit('setRegisterResult', r);

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
    setRequestRegisterWholesaleResult(state, requestRegisterWholesaleResult) {
        state.requestRegisterWholesaleResult = requestRegisterWholesaleResult;
    },
    setRegisterResult(state, registerResult) {
        state.registerResult = registerResult;
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}