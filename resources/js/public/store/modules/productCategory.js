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
        api.getData('productCategory', {})
            .then(r => {
                if (r.result === true) {
                    commit('setAll', r.returnData);
                }
            })
            .catch()
    },
    getAllById({commit}) {
        api.getData('productCategory', {'indexBy': 'id'})
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
    setAll(state, productsCategory) {
        state.all = productsCategory
    },
    setAllById(state, productsCategory) {
        state.allById = {...state.allById, ...productsCategory}
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}