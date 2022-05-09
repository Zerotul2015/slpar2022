import api from '../../common/api'

// initial state
const state = () => ({
    all: [],
})

// getters
const getters = {
    all(state) {
        return state.all;
    },
    byId: (state) => (id) => {
        return state.all.find(item => item.id === id);
    }
}

// actions
const actions = {
    getAll({commit, state}) {
            api.getData('wholesaleLevel', {})
                .then(r => {
                    if (r.result === true) {
                        commit('setAll', r.returnData);
                    }
                })
                .catch()
    },
}

// mutations
const mutations = {
    setAll(state, all) {
        state.all = all;
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}