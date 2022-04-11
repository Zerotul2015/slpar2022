import api from '../../common/api'

// initial state
const state = () => ({
    categoryData: [],
})

// getters
const getters = {
    url(state) {
        return state.categoryData.url ? state.categoryData.url : '';
    },
    name(state) {
        return state.categoryData.name ? state.categoryData.name : '';
    },
    description(state){
        return state.categoryData.description ? state.categoryData.description : '';
    },
    folder(state){
        return state.categoryData.folder ? state.categoryData.folder : '';
    },
    image(state){
        return state.categoryData.image ? state.categoryData.image : null;
    }
}

// actions
const actions = {
    getCategory({commit}, id) {
        let sendData ={
            'where':'id',
            'searchString':id
        }
        api.getData('productCategory', sendData)
            .then(r => {
                if (r.result === true) {
                    commit('setCategory', r.returnData);
                }
            })
            .catch()
    },
}

// mutations
const mutations = {
    setCategory(state, productsCategory) {
        state.categoryData = productsCategory;
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}