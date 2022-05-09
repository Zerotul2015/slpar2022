import Vue from "vue";
import Router from "vue-router";

Vue.use(Router);

export default new Router({
    mode: 'history',
    base: '/',
    routes: [
        {
            path: '',
            name: 'index',
            meta: {
                allowAnonymous: true,
                allowCustomer: true,
                allowWholesale: true,
            },
            component: () => import("../components/BathStyle/BathStyle.vue"),
        },
        {
            path: '/page/:url',
            name: 'page',
            props: true,
            meta: {
                allowAnonymous: true,
                allowCustomer: true,
                allowWholesale: true,
            },
            component: () => import("../components/Page/Page.vue"),
        },
        {
            path: '/page-category/:url',
            name: 'pageCategory',
            props: true,
            meta: {
                allowAnonymous: true,
                allowCustomer: true,
                allowWholesale: true,
            },
            component: () => import("../components/Page/PageCategory.vue"),
        },
        {
            path: '/product/:url',
            name: 'product',
            props: true,
            meta: {
                allowAnonymous: true,
                allowCustomer: true,
                allowWholesale: true,
            },
            component: () => import("../components/Product/ProductPage.vue"),
        },
        {
            path: '/catalog/:urlParent?/:url/style-:styleUrl?',
            name: 'productCategoryWithStyle',
            props: true,
            meta: {
                allowAnonymous: true,
                allowCustomer: true,
                allowWholesale: true,
            },
            component: () => import("../components/Category/CategoryPage.vue"),
        },
        {
            path: '/catalog/:urlParent?/:url',
            name: 'productCategory',
            props: true,
            meta: {
                allowAnonymous: true,
                allowCustomer: true,
                allowWholesale: true,
            },
            component: () => import("../components/Category/CategoryPage.vue"),
        },
        {
            path: '/bath-style/:url*',
            name: 'bathStyle',
            props: true,
            meta: {
                allowAnonymous: true,
                allowCustomer: true,
                allowWholesale: true,
            },
            component: () => import("../components/BathStyle/BathStyle.vue"),
        },
        {
            path: '/cart',
            name: 'cart',
            meta: {
                allowAnonymous: true,
                allowCustomer: true,
                allowWholesale: true,
            },
            component: () => import("../components/Cart/Cart.vue"),
        },
        {
            path: '/compare',
            name: 'compare',
            meta: {
                allowAnonymous: true,
                allowCustomer: true,
                allowWholesale: true,
            },
            component: () => import("../components/Compare/Compare.vue"),
        },
        {
            path: '/orders/preview',
            name: 'ordersPreview',
            props: true,
            meta: {
                allowAnonymous: true,
                allowCustomer: true,
                allowWholesale: true,
            },
            component: () => import("../components/Orders/Orders.vue"),
        },
        {
            path: '/customer/register',
            name: 'customerRegister',
            meta: {
                allowAnonymous: true,
                allowCustomer: true,
                allowWholesale: true,
            },
            component: () => import("../components/Customer/CustomerHomePage.vue"),
        },
        {
            path: '/customer/home',
            name: 'customerHome',
            meta: {
                allowAnonymous: true,
                allowCustomer: true,
                allowWholesale: true,
            },
            component: () => import("../components/Customer/CustomerHomePage.vue"),
        },
        {
            path: '/customer/order-form',
            name: 'wholesaleOrderForm',
            meta: {
                allowAnonymous: false,
                allowCustomer: false,
                allowWholesale: true,
            },
            component: () => import("../components/Customer/CustomerWholesaleOrderForm.vue"),
        },
        {
            path: '/customer/profile',
            name: 'customerProfile',
            allowAnonymous: false,
            allowCustomer: true,
            allowWholesale: false,
            component: () => import("../components/Customer/CustomerProfile.vue"),
        },
        {
            path: '/customer/wholesale-profile',
            name: 'wholesaleProfile',
            allowAnonymous: false,
            allowCustomer: false,
            allowWholesale: true,
            component: () => import("../components/Customer/CustomerWholesaleProfile.vue"),
        },
        {
            path: '/403',
            name: 'AccessRestricted',
            component: () => import("../components/Errors/AccessRestricted.vue"),
        },
        {
            path: '*',
            name: 'NotFoundPage',
            component: () => import("../components/Errors/NotFoundPage.vue"),
        },
    ]
});