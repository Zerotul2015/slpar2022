import api from '../../common/api'

// initial state
const state = () => ({
    allById: {},
})

// getters
const getters = {
    all(state) {
        return state.allById;
    },
    byId: (state) => (id) => {
        if(state.allById && state.allById[id]) {
            return state.allById[id];
        }else{
            return null;
        }
    }
}

// actions
const actions = {
    getAll({commit, state}) {
            api.getData('customer', {})
                .then(r => {
                    if (r.result === true) {
                        commit('setAll', r.returnData);
                    }
                })
                .catch()
    },
    customerById({commit, state}, id) {
        if (id) {
            api.getData('customer', {'where': 'id', 'searchString': id})
                .then(r => {
                    if (r.result === true) {
                        commit('addCustomer', r.returnData[0]);
                    }
                })
                .catch()
        }
    },
}

// mutations
const mutations = {
    setAllById(state, all) {
        state.allById = all;
    },
    addCustomer(state, customer) {
        state.allById[customer.id] = customer;
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}