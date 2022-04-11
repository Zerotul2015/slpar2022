import api from '../../common/api'

// initial state
const state = () => ({
    compare: {}
})

// getters
const getters = {

}

// actions
const actions = {
    getCompare({commit}) {
        api.getData('compare', {})
            .then(r => {
                if (r.result === true) {
                    commit('setCompare', r.returnData);
                }
            })
            .catch()
    },
}

// mutations
const mutations = {
    setCompare(state, templateData) {
        state.compare = templateData
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}