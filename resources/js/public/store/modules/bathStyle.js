import api from '../../common/api'

// initial state
const state = () => ({
    selectedStyleKey: 0,
    selectedStyleId: null,
    bathStylesById: {},
    bathStyles: {},
})

// getters
const getters = {
    selectKey(state) {
        return state.selectedStyleKey;
    },
    selectId(state) {
        return state.selectedStyleId;
    },
    //получаем стиль с переданным id
    bathStyle: (state) => (id) => {
        if (id) {
            if (state.bathStylesById[id]) {
                return state.bathStylesById[id];
            } else {
                return undefined;
            }
        } else {
            return Object.values(state.bathStyle)[0];
        }
    },
    all(state) {
        return state.bathStyles;
    },
    allById(state) {
        return state.bathStylesById;
    },
}

// actions
const actions = {
    setActiveStyle({commit, state}, keyStyle) {
        if (state.bathStyles[keyStyle]) {
            commit('setActiveStyleKey', keyStyle);
            let id = state.bathStyles[keyStyle].id;
            commit('setActiveStyleId', id);
        }

    },
    getAll({commit}) {
        api.getData('bathStyle', {})
            .then(r => {
                if (r.result === true) {
                    commit('setAll', r.returnData);
                }
            })
            .catch()
    },
    getAllById({commit}) {
        api.getData('bathStyle', {'indexBy': 'id'})
            .then(r => {
                if (r.result === true) {
                    commit('setAllById', r.returnData);
                }
            })
            .catch()
    },
}

// mutations
const mutations = {
    setActiveStyleKey(state, keyStyle) {
        state.selectedStyleKey = keyStyle;
    },
    setActiveStyleId(state, idStyle) {
        state.selectedStyleKId = idStyle;
    },
    setAll(state, bathStyles) {
        state.bathStyles = bathStyles;
    },
    setAllById(state, bathStyles) {
        state.bathStylesById = bathStyles;
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}