import api from '../../common/api'

// initial state
const state = () => ({
    methods: [],

})

// getters
const getters = {
    methods(state) {
        return state.methods;
    },

}

// actions
const actions = {
    getMethods({commit}) {
        let sendData = {
            'where': 'enable',
            'searchString': 1
        }
        api.getData('paymentMethods', sendData)
            .then(r => {
                if (r.result === true) {
                    if (r.returnData) {
                        commit('setMethods', r.returnData);
                    }
                }
            })
            .catch()
    }
}

// mutations
const mutations = {
    setMethods(state, methods) {
        state.methods = methods;
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}