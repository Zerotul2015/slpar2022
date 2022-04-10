import Vue from 'vue'
import Vuex from 'vuex'
import banners from './modules/banners'
import page from './modules/page'
import pageCategory from './modules/pageCategory'
import bathStyle from './modules/bathStyle'
import product from './modules/product'
import productStockStatus from './modules/productStockStatus'
import productUnit from './modules/productUnit'
import productManufacturer from './modules/productManufacturer'
import productCategory from './modules/productCategory'

Vue.use(Vuex)

export default new Vuex.Store({
    modules:{
        banners,
        page,
        pageCategory,
        bathStyle,
        product,
        productStockStatus,
        productUnit,
        productManufacturer,
        productCategory,
    },
    state: {

    },
    mutations: {

    }
})