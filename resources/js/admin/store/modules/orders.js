import api from '../../common/api'

// initial state
const state = () => ({
    all: [],
    allById: {}
})

// getters
const getters = {}

// actions
const actions = {
    getAll({commit}) {
        api.getData('orders', {})
            .then(r => {
                if (r.result === true) {
                    commit('setAll', r.returnData);
                }
            })
            .catch()
    },
    getAllById({commit}) {
        api.getData('orders', {'indexBy': 'id'})
            .then(r => {
                if (r.result === true) {
                    commit('setAllById', r.returnData);
                }
            })
            .catch()
    }
}

// mutations
const mutations = {
    setAll(state, orders) {
        state.all = orders
    },
    setAllById(state, orders) {
        state.allById = {...state.allById, ...orders}
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}