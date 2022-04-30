import api from '../../common/api'

// initial state
const state = () => ({
    breadcrumb: '',
    menuCatalog: {},
    menuHeader: {},
    footer: {},
    section: 'index', //index,page,pageCategory,product,productCategory,cart,compare,favorite,bathStyle Берется из названия route
    sectionKey: null, // Берется из параметра url в router
    seo: {'title': '', 'description': ''},
    gridSizeCompare: 'big', //big, default
    gridSizeCategory: 'default', //big, default
})

// getters
const getters = {
    breadcrumb(state) {
        return state.breadcrumb;
    },
    menuCatalog(state) {
        return state.menuCatalog;
    },
    menuHeader(state) {
        return state.menuHeader;
    },
    footer(state) {
        return state.footer;
    },
    seo(state) {
        return state.seo;
    },
    section(state) {
        return state.section;
    },
    sectionKey(state) {
        return state.sectionKey;
    },
    gridSizeCompare(state) {
        return state.gridSizeCompare;
    },
    gridSizeCategory(state) {
        return state.gridSizeCategory;
    },
}

// actions
const actions = {
    getChange({commit, state}) {
        let sendData = {
            'section':state.section,
            'sectionKey':state.sectionKey,
            'simple': true,
        };
        api.getData('templateData', sendData)
            .then(r => {
                if (r.result === true) {
                    commit('setSeo', r.returnData.seo);
                    commit('setBreadcrumb', r.returnData.breadcrumb);
                }
            })
            .catch()
    },
    getTemplateSettings({commit, state}) {
        let sendData = {
            'section':state.section,
            'sectionKey':state.sectionKey,
        };
        api.getData('templateData', sendData)
            .then(r => {
                if (r.result === true) {
                    commit('setSeo', r.returnData.seo);
                    commit('setBreadcrumb', r.returnData.breadcrumb);
                    commit('setMenuCatalog', r.returnData.menuCatalog);
                    commit('setMenuHeader', r.returnData.menuHeader);
                    commit('setFooter', r.returnData.footer);
                }
            })
            .catch()
    },
}

// mutations
const mutations = {
    setSeo(state, newSeo) {
        state.seo = newSeo;
    },
    setSection(state, newSection) {
        state.section = newSection;
    },
    setSectionKey(state, newSectionKey) {
        state.sectionKey = newSectionKey;
    },
    setBreadcrumb(state, breadcrumb) {
        state.breadcrumb = breadcrumb;
    },
    setMenuCatalog(state, menuCatalog) {
        state.menuCatalog = menuCatalog;
    },
    setMenuHeader(state, menuHeader) {
        state.menuHeader = menuHeader;
    },
    setFooter(state, footer) {
        state.footer = footer;
    },
    setGridSizeCompare(state, gridSize){
        if(gridSize === 'big'){
            state.gridSizeCompare = 'big';
        }else{
            state.gridSizeCompare = 'default';
        }
    },
    setGridSizeCategory(state, gridSize){
        if(gridSize === 'big'){
            state.gridSizeCategory = 'big';
        }else{
            state.gridSizeCategory = 'default';
        }
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}