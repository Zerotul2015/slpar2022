import api from '../../common/api'

// initial state
const state = () => ({
    all: [],
    allById: {},
})

// getters
const getters = {
}

// actions
const actions = {
    getAll({commit}) {
        api.getData('customerCompany', {})
            .then(r => {
                if (r.result === true) {
                    commit('setAll', r.returnData);
                }
            })
            .catch()
    },
    getAllById({commit}) {
        api.getData('customerCompany', {'indexBy': 'id'})
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
    setAll(state, customerCompany) {
        state.all = customerCompany
    },
    setAllById(state, customerCompany) {
        state.allById = {...state.allById, ...customerCompany}
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}