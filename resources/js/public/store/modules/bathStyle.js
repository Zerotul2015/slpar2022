import api from '../../common/api'

// initial state
const state = () => ({
    selectedStyleKey: 0,
    selectedStyleId: null,
    styleUrl: '',
    bathStyles: [],
    filterBy:'', //fireplace | bath
    filterToggle:false,
    productsData: {},
    productsCategoryData: {}
})

// getters
const getters = {
    selectKey(state) {
        return state.selectedStyleKey;
    },
    selectId(state) {
        return state.selectedStyleId;
    },
    filterBy(state) {
        return state.filterBy;
    },
    filterToggle(state) {
        return state.filterToggle;
    },
    bathStylesByUrl: (state) => (url) => {
        let bathStyle = state.bathStyles.filter(item => item.url === url);
        return bathStyle.length > 0 ? bathStyle[0] : null;
    },
    bathStylesById: (state) => (id) => {
        let bathStyle = state.bathStyles.filter(item => item.id === id);
        return bathStyle.length > 0 ? bathStyle[0] : null;
    },
    all(state) {
        return state.bathStyles;
    },
    productsData(state) {
        return state.productsData;
    },
    productsCategoryData(state) {
        return state.productsCategoryData;
    },
}

// actions
const actions = {
    setFilter({commit} ,filterName){
        if (filterName === 'fireplace' || filterName === 'bath') {
            commit('setFilterBy', filterName);
            commit('setFilterToggle', true);
        }

    },
    disableFilter({commit}){
        commit('setFilterToggle', false);
        commit('setFilterBy', null);
    },
    getProductsData({commit, state}) {
        //getProductsData
        if (state.bathStyles[state.selectedStyleKey]) {
            let sendData = {'getProductsData': true, 'bathStyleId': state.bathStyles[state.selectedStyleKey].id};
            api.getData('bathStyle', sendData)
                .then(r => {
                    if (r.result === true) {
                        commit('setProductsData', r.returnData.products);
                        commit('setProductsCategoryData', r.returnData.productsCategory);
                    }
                })
                .catch()
        }
    },
    setActiveStyleKeyByUrl({commit, state}, urlStyle) {
        let keyStyle = state.bathStyles.findIndex(item => item.url === urlStyle);
        keyStyle = keyStyle !== -1 ? keyStyle : 0;
        commit('setActiveStyleKey', keyStyle);
    },
    setActiveStyleKey({commit, state}, keyStyle) {
        if (state.bathStyles[keyStyle]) {
            commit('setActiveStyleKey', keyStyle);
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
    setFilterBy(state, filterName) {
        if (filterName === 'fireplace' || filterName === 'bath') {
            state.filterBy = filterName;
        }
    },
    setFilterToggle(state, toggleStatus) {
        state.filterToggle = !!toggleStatus;
    },
    setStyleUrl(state, styleUrl) {
        state.styleUrl = styleUrl;
    },
    setActiveStyleKey(state, keyStyle) {
        state.selectedStyleKey = keyStyle;
    },
    setProductsData(state, productsData) {
        state.productsData = productsData;
    },
    setProductsCategoryData(state, productsCategoryData) {
        state.productsCategoryData = productsCategoryData;
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