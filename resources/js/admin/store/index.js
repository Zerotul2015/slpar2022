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
import customer from './modules/customer'
import customers from './modules/customers'
import customerCompany from './modules/customerCompany'
import orders from './modules/orders'
import ordersStatus from './modules/ordersStatus'
import galleryCategory from './modules/galleryCategory'
import wholesaleCustomer from './modules/wholesale_customer'
import wholesaleLevel from './modules/wholesale_level'

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
        customer,
        customers,
        customerCompany,
        orders,
        ordersStatus,
        galleryCategory,
        wholesaleCustomer,
        wholesaleLevel,
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