import api from '../../common/api'

// initial state
const state = () => ({
    allById: {},
})

// getters
const getters = {
    all(state) {
        return state.allById;
    },
    byId: (state) => (id) => {
        if(state.allById && state.allById[id]) {
            return state.allById[id];
        }else{
            return null;
        }
    }
}

// actions
const actions = {
    getAll({commit, state}) {
            api.getData('ordersStatus', {})
                .then(r => {
                    if (r.result === true) {
                        commit('setAll', r.returnData);
                    }
                })
                .catch()
    },
    getStatusById({commit, state}, id) {
        if (id) {
            api.getData('ordersStatus', {'where': 'id', 'searchString': id})
                .then(r => {
                    if (r.result === true) {
                        commit('addStatus', r.returnData[0]);
                    }
                })
                .catch()
        }
    },
}

// mutations
const mutations = {
    setAllById(state, all) {
        state.allById = all;
    },
    addStatus(state, status) {
        state.allById[status.id] = status;
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}