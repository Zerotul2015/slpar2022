import Vue from 'vue'
import Vuex from 'vuex'
import banners from './modules/banners'
import page from './modules/page'
import pageCategory from './modules/pageCategory'
import product from './modules/product'
import productStockStatus from './modules/productStockStatus'
import productUnit from './modules/productUnit'
import productManufacturer from './modules/productManufacturer'
import productCategory from './modules/productCategory'
import customer from './modules/customer'
import customerCompany from './modules/customerCompany'
import orders from './modules/orders'
import galleryCategory from './modules/galleryCategory'

Vue.use(Vuex)

export default new Vuex.Store({
    modules:{
        banners,
        page,
        pageCategory,
        product,
        productStockStatus,
        productUnit,
        productManufacturer,
        productCategory,
        customer,
        customerCompany,
        orders,
        galleryCategory,
    },
    state: {
        authData: {
            isAuth:null,
            accessLevel:null,
            tokenAuth:null
        },
    },
    mutations: {
        authChange (state, payload) {
            state.authData = payload;
        },
    }
})