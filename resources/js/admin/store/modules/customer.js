import api from '../../common/api'

// initial state
const state = () => ({
    customer:{},
    orders: [],
    company: [],
    wholesaleData:{},
})

// getters
const getters = {
    customer(state){
        return state.customer;
    },
    orders(state){
        return state.orders;
    },
    company(state){
        return state.company;
    },
}

// actions
const actions = {
    setCustomerData({commit}, customer){
        commit('setCustomer', customer);
    },
    getRelatedData({commit, dispatch, state}, id) {
        if (state.customer && state.customer.id) {
            dispatch('getOrders');
            dispatch('getCompany');
            if(state.customer.is_wholesale === true) {
                dispatch('getWholesaleData');
            }
        }
    },
    getCustomer({commit, state}, id) {
        if (id) {
            api.getData('customer', {
                'where': 'id',
                'searchString': id
            })
                .then(r => {
                    if (r.result === true && r.returnData && r.returnData[0]) {
                        commit('setCustomer', r.returnData[0]);
                    }
                })
                .catch()
        }
    },
    getWholesaleData({commit, state}) {
        if (state.customer && state.customer.id) {
            api.getData('wholesaleCustomer', {
                'where': 'customer_id',
                'searchString': state.customer.id
            })
                .then(r => {
                    if (r.result === true) {
                        commit('setWholesaleData', r.returnData[0]);
                    }
                })
                .catch()
        }
    },
    getOrders({commit, state}) {
        if (state.customer && state.customer.id) {
            api.getData('orders', {
                'where': 'customer',
                'searchString': state.customer.id
            })
                .then(r => {
                    if (r.result === true) {
                        commit('setOrders', r.returnData);
                    }
                })
                .catch()
        }
    },
    getCompany({commit, state}) {
        if (state.customer && state.customer.id) {
            api.getData('customerCompany', {
                'where': 'customer_id',
                'searchString': state.customer.id
            })
                .then(r => {
                    if (r.result === true) {
                        commit('setCompany', r.returnData);
                    }
                })
                .catch()
        }
    }
}

// mutations
const mutations = {
    setCustomer(state, customer) {
        state.customer = customer;
    },
    setOrders(state, orders) {
        state.orders = orders;
    },
    setWholesaleData(state, wholesaleData) {
        state.wholesaleData = wholesaleData;
    },
    setCompany(state, company) {
        state.company = company;
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}