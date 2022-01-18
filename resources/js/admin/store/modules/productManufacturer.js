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
    getAll ({ commit }) {
        api.getData('productManufacturer',{})
            .then(r=>{
                if(r.result === true) {
                    commit('setAll', r.returnData);
                }
            })
            .catch()
    },
    getAllById ({ commit }) {
        api.getData('productManufacturer', {'indexBy':'id'})
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
    setAll (state, productsManufacturers) {
        state.all = productsManufacturers
    },
    setAllById (state, productsManufacturers) {
        state.allById = {...state.allById, ...productsManufacturers}
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}