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
        api.getData('page',{})
            .then(r=>{
                if(r.result === true) {
                    commit('setAll', r.returnData);
                }
            })
            .catch()
    },
    getAllById ({ commit }) {
        api.getData('page', {'indexBy':'id'})
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
    setAll (state, pages) {
        state.all = pages
    },
    setAllById (state, pages) {
        state.allById = {...state.allById, ...pages}
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}