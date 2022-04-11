import api from '../../common/api'

// initial state
const state = () => ({
    favorite: {}
})

// getters
const getters = {

}

// actions
const actions = {
    getFavorite({commit}) {
        api.getData('favorite', {})
            .then(r => {
                if (r.result === true) {
                    commit('setFavorite', r.returnData);
                }
            })
            .catch()
    },
}

// mutations
const mutations = {
    setFavorite(state, templateData) {
        state.favorite = templateData
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}