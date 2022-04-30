import api from '../../common/api'

// initial state
const state = () => ({
    page: {},

})

// getters
const getters = {
    page(state) {
        return state.page;
    },
}

// actions
const actions = {
    getByUrl({commit}, url) {
        let sendData = {
            'where': 'url',
            'searchString': url
        }
        api.getData('page', sendData)
            .then(r => {
                if (r.result === true) {
                    if (r.returnData[0]) {
                        commit('setPage', r.returnData[0]);
                    }
                }
            })
            .catch()
    }
}

// mutations
const mutations = {
    setPage(state, page) {
        state.page = page;
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}