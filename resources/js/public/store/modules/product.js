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
        api.getData('product', {})
            .then(r => {
                if (r.result === true) {
                    commit('setAll', r.returnData);
                }
            })
            .catch()
    },
    getAllById({commit}) {
        api.getData('product', {'indexBy': 'id'})
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
    setAll(state, products) {
        state.all = products
    },
    setAllById(state, products) {
        state.allById = {...state.allById, ...products}
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}