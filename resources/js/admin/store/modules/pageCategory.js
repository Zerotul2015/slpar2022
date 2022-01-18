import api from '../../common/api'

// initial state
const state = () => ({
    all: [],
    allById:{}
})

// getters
const getters = {}

// actions
const actions = {
    getAll ({ commit }) {
        api.getData('pageCategory',{})
            .then(r=>{
                if(r.result === true) {
                    commit('setAll', r.returnData);
                }
            })
            .catch()
    },
    getAllById ({ commit }) {
        api.getData('pageCategory', {'indexBy':'id'})
            .then(r=>{
                if(r.result === true) {
                    commit('setAllById', r.returnData);
                }
            })
            .catch()
    }
}

// mutations
const mutations = {
    setAll (state, pageCategory) {
        state.all = pageCategory
    },
    setAllById (state, pageCategory) {
        state.allById = {...state.allById, ...pageCategory}
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}