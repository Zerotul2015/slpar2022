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
        api.getData('productStockStatus', {})
            .then(r => {
                if (r.result === true) {
                    commit('setAll', r.returnData);
                }
            })
            .catch()
    },
    getAllById({commit}) {
        api.getData('productStockStatus', {'indexBy': 'id'})
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
    setAll(state, productsStockStatus) {
        state.all = productsStockStatus
    },
    setAllById(state, productsStockStatus) {
        state.allById = {...state.allById, ...productsStockStatus}
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}