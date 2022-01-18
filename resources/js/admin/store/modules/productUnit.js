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
        api.getData('productUnit',{})
            .then(r=>{
                if(r.result === true) {
                    commit('setAll', r.returnData);
                }
            })
            .catch()
    },
    getAllById ({ commit }) {
        api.getData('productUnit', {'indexBy':'id'})
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
    setAll (state, productsUnits) {
        state.all = productsUnits
    },
    setAllById (state, productsUnits) {
        state.allById = {...state.allById, ...productsUnits}
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}