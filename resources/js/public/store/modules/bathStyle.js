import api from '../../common/api'

// initial state
const state = () => ({
    selectedStyleKey: 0,
    selectedStyleId: null,
    bathStyles: [],
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
    bathStylesByUrl:(state)=>(url)=>{
        let bathStyle = state.bathStyles.filter(item=>item.url === url);
        return bathStyle.length > 0 ? bathStyle[0] : null;
    },
    bathStylesById:(state)=>(id)=>{
        let bathStyle = state.bathStyles.filter(item=>item.id === id);
        return bathStyle.length > 0 ? bathStyle[0] : null;
    },
    all(state) {
        return state.bathStyles;
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
    }
}

// mutations
const mutations = {
    setActiveStyleKey(state, keyStyle) {
        state.selectedStyleKey = keyStyle;
    },
    setAll(state, bathStyles) {
        state.bathStyles = bathStyles;
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}