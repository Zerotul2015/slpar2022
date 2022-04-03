import api from '../../common/api'

// initial state
const state = () => ({
    id: null,
    profile:{
        'id': null,
        'name': null,
        'phone': null,
        'mail': null,
        'status': null,
        'note_hidden': null,
    },
    orders: [],
    company: [],
    notFound:null,
})

// getters
const getters = {}

// actions
const actions = {
    reset({commit, state}){
        commit('setId', null);
        commit('setOrders', []);
        commit('setProfile', {
            'id': null,
            'name': null,
            'phone': null,
            'mail': null,
            'status': null,
            'note_hidden': null,
        });
        commit('setCompany', []);
    },
    getData({commit, dispatch, state}, params) {
        if(params.id){
            commit('setId', params.id);
        }
        if (state.id) {
            dispatch('getCustomer');
            dispatch('getOrders');
            dispatch('getCompany');
        }
    },
    getCustomer({commit, state}) {
        if (state.id) {
            api.getData('customer', {
                'where': 'id',
                'searchString': state.id
            })
                .then(r => {
                    if (r.result === true && r.returnData && r.returnData[0]) {
                        commit('setProfile', {'profile':r.returnData[0]});
                        commit('setNotFound', false);
                    }else{
                        commit('setNotFound', true);
                    }
                })
                .catch()
        }
    },
    getOrders({commit, state}) {
        if (state.id) {
            api.getData('orders', {
                'where': 'customer',
                'searchString': state.id
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
        if (state.id) {
            api.getData('customerCompany', {
                'where': 'customer_id',
                'searchString': state.id
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
    setId(state, id) {
        state.id = id;
    },
    setOrders(state, orders) {
        state.orders = orders;
    },
    setCompany(state, company) {
        state.company = company;
    },
    setNotFound(state, notFound) {
        state.notFound = notFound;
    },
    setProfile(state, payload){
        if(payload.profile){
            state.profile = {...state.profile, ...payload.profile}
        }
        if(payload.changedPass && payload.changedPass === true) {
            state.profile['pass'] = payload.profile.pass ? payload.profile.pass : state.profile.pass ? state.profile.pass : null;
        }
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}