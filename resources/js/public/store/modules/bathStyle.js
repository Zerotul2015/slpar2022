import api from '../../common/api'

// initial state
const state = () => ({
    activeStyleKey: 0, //number
    activeStyleId: null, //number
    styleUrl: '',
    bathStyles: [],
    filterBy: '', //fireplace | bath | homestead
    filterToggle: false,
    toggleFilterForCategory: false, //
    productsData: {},
    productsCategoryData: {},

})

// getters
const getters = {
    activeKey(state) {
        return state.activeStyleKey;
    },
    activeId(state) {
        return state.activeStyleId;
    },
    filterBy(state) {
        return state.filterBy;
    },
    filterToggle(state) {
        return state.filterToggle;
    },
    toggleFilterForCategory(state) {
        return state.toggleFilterForCategory;
    },
    bathStylesByUrl: (state) => (url) => {
        return state.bathStyles.find(item => item.url === url);
        //return bathStyle.length > 0 ? bathStyle[0] : null;
    },
    bathStylesById: (state) => (id) => {
        return state.bathStyles.find(item => item.id === id);
        //return bathStyle.length > 0 ? bathStyle[0] : null;
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
    changeToggleFilterForCategory({commit}, statusNew) {
        statusNew = !!statusNew;
        commit('setToggleFilterForCategory', statusNew);
    },
    resetFilterForCategory({commit}){
        commit('setActiveStyleId', null);
        commit('setToggleFilterForCategory', false);

    },
    setFilter({commit}, filterName) {
        if (filterName === 'fireplace' || filterName === 'bath' || filterName === 'homestead') {
            commit('setFilterBy', filterName);
            commit('setFilterToggle', true);
        }
    },
    disableFilter({commit}) {
        commit('setFilterToggle', false);
        commit('setFilterBy', null);
    },
    getProductsData({commit, state}) {
        //getProductsData
        if (state.bathStyles[state.activeStyleKey]) {
            let sendData = {'getProductsData': true, 'bathStyleId': state.bathStyles[state.activeStyleKey].id};
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
    setActiveStyleByUrl({commit, state}, urlStyle) {
        console.log(urlStyle)
        let keyStyle = state.bathStyles.findIndex(item => item.url === urlStyle);
        keyStyle = keyStyle !== -1 ? keyStyle : 0;
        commit('setActiveStyleKey', keyStyle);
        if (state.bathStyles[keyStyle]) {
            commit('setActiveStyleId', state.bathStyles[keyStyle].id);
        }
    },
    setActiveStyleById({commit, state}, idStyle) {
        let keyStyle = state.bathStyles.findIndex(item => item.id === idStyle);
        keyStyle = keyStyle !== -1 ? keyStyle : 0;
        commit('setActiveStyleKey', keyStyle);
        commit('setActiveStyleId', idStyle);
    },
    setActiveStyleKey({commit, state}, keyStyle) {
        if (state.bathStyles[keyStyle]) {
            commit('setActiveStyleKey', keyStyle);
            commit('setActiveStyleId', state.bathStyles[keyStyle].id);
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
    setToggleFilterForCategory(state, statusNew){
        state.toggleFilterForCategory = statusNew;
    },
    setFilterBy(state, filterName) {
        if (filterName === 'fireplace' || filterName === 'bath' || filterName === 'homestead') {
            state.filterBy = filterName;
        } else {
            state.filterBy = '';
        }
    },
    setFilterToggle(state, toggleStatus) {
        state.filterToggle = !!toggleStatus;
    },
    setStyleUrl(state, styleUrl) {
        state.styleUrl = styleUrl;
    },
    setActiveStyleKey(state, keyStyle) {
        state.activeStyleKey = keyStyle;
    },
    setActiveStyleId(state, keyStyle) {
        state.activeStyleId = keyStyle;
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