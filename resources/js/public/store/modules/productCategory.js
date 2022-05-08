import api from '../../common/api'

// initial state
const state = () => ({
    categoryData: [],
    products: [],
})

// getters
const getters = {
    category(state) {
        return state.categoryData;
    },
    url(state) {
        return state.categoryData.url ? state.categoryData.url : '';
    },
    name(state) {
        return state.categoryData.name ? state.categoryData.name : '';
    },
    description(state) {
        return state.categoryData.description ? state.categoryData.description : '';
    },
    folder(state) {
        return state.categoryData.folder ? state.categoryData.folder : '';
    },
    image(state) {
        return state.categoryData.image ? state.categoryData.image : null;
    },
    products(state) {
        return state.products;
    },
}

// actions
const actions = {
    getProducts({commit, state}) {
        if (state.categoryData.id) {
            let sendData = {
                'where': 'category_id',
                'searchString': state.categoryData.id
            }
            api.getData('product', sendData)
                .then(r => {
                    if (r.result === true) {
                        commit('setProducts', r.returnData);
                    }
                })
                .catch()
        } else {
            //temp for blank order
            api.getData('product', {})
                .then(r => {
                    if (r.result === true) {
                        commit('setProducts', r.returnData);
                    }
                })
                .catch()
        }
    },
    getCategoryByUrl({commit, dispatch}, url) {
        let sendData = {
            'where': 'url',
            'searchString': url
        }
        api.getData('productCategory', sendData)
            .then(r => {
                if (r.result === true) {
                    if (r.returnData[0]) {
                        commit('setCategory', r.returnData[0]);
                        dispatch('getProducts');
                    }
                }
            })
            .catch()
    },
}

// mutations
const mutations = {
    setProducts(state, products) {
        state.products = products;
    },
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