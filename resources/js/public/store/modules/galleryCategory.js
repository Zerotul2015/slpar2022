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
        api.getData('galleryCategory',{})
            .then(r=>{
                if(r.result === true) {
                    commit('setAll', r.returnData);
                }
            })
            .catch()
    },
    getAllById ({ commit }) {
        api.getData('galleryCategory', {'indexBy':'id'})
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
    setAll (state, galleryCategory) {
        state.all = galleryCategory
    },
    setAllById (state, galleryCategory) {
        state.allById = {...state.allById, ...galleryCategory}
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}