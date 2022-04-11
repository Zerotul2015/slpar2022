import api from '../../common/api'

// initial state
const state = () => ({
    selectedStyle: {},
    bathStyle: {},
    bathStylesById: {},
})

// getters
const getters = {
    //получаем стиль с переданным id
    bathStyle: (state) => (id) => {
        if (state.bathStylesById[id]) {
            return state.bathStylesById[id];
        } else {
            return undefined;
        }
    },
    //для получения данных из bathStyle
    url(state) {
        return state.bathStyle.url ? state.bathStyle.url : '';
    },
    name(state) {
        return state.bathStyle.name ? state.bathStyle.name : '';
    },
    description(state) {
        return state.bathStyle.description ? state.bathStyle.description : '';
    },
    folder(state) {
        return state.bathStyle.folder ? state.bathStyle.folder : '';
    },
    image(state) {
        return state.bathStyle.image ? state.bathStyle.image : null;
    }
}

// actions
const actions = {
    getAllById({commit}) {
        api.getData('bathStyle', {'indexBy': 'id'})
            .then(r => {
                if (r.result === true) {
                    commit('setAllById', r.returnData);
                }
            })
            .catch()
    },
    selectStyle({commit, state}, id) {
        if (state.bathStylesById[id]) {
            commit('setBathStyle', state.bathStylesById[id]);
        }

    }
}

// mutations
const mutations = {
    setBathStyle(state, bathStyle) {
        state.bathStyle = bathStyle;
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