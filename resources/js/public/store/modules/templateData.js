import api from '../../common/api'

// initial state
const state = () => ({
    templateSettings: {}
})

// getters
const getters = {
    breadcrumb(state) {
        return state.templateSettings.breadcrumb ? state.templateSettings.breadcrumb : '';
    },
    bathStyles(state) {
        return state.templateSettings.bathStyles ? state.templateSettings.bathStyles : {};
    },
    menuCatalog(state) {
        return state.templateSettings.menuCatalog ? state.templateSettings.menuCatalog : '';
    },
    footer(state) {
        return state.templateSettings.footer ? state.templateSettings.footer : {};
    },
    seo(state) {
        return state.templateSettings.seo ? state.templateSettings.seo : '';
    }
}

// actions
const actions = {
    getTemplateSettings({commit}) {
        api.getData('templateData', {})
            .then(r => {
                if (r.result === true) {
                    commit('setTemplateSettings', r.returnData);
                }
            })
            .catch()
    },
}

// mutations
const mutations = {
    setTemplateSettings(state, templateData) {
        state.templateSettings = templateData
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}