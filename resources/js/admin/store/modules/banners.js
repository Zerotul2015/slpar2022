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
        api.getData('banners',{})
            .then(r=>{
                if(r.result === true) {
                    commit('setAll', r.returnData);
                }
            })
            .catch()
    },
    getAllById ({ commit }) {
        api.getData('banners', {'indexBy':'id'})
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
    setAll (state, banners) {
        state.all = banners
    },
    setAllById (state, banners) {
        state.allById = {...state.allById, ...banners}
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}