import Vue from "vue";
import Router from "vue-router";

Vue.use(Router);

export default new Router({
    mode: 'history',
    base: '/',
    routes: [
        {
            path: '/',
            name: 'index',
            component: () => import("../components/BathStyle/BathStyle.vue"),
        },
        {
            path: '/page/:url',
            name: 'page',
            props: true,
            component: () => import("../components/Page/Page.vue"),
        },
        {
            path: '/page-category/:url',
            name: 'pageCategory',
            props: true,
            component: () => import("../components/Page/PageCategory.vue"),
        },
        {
            path: '/product/:url',
            name: 'product',
            props: true,
            component: () => import("../components/Product/ProductPage.vue"),
        },
        {
            path: '/catalog/:urlParent*/:url',
            name: 'productCategory',
            props: true,
            component: () => import("../components/Category/CategoryPage.vue"),
        },
        {
            path: '/bath-style/:url*',
            name: 'bathStyle',
            props: true,
            component: () => import("../components/BathStyle/BathStyle.vue"),
        },
        {
            path: '/cart',
            name: 'cart',
            props: true,
            component: () => import("../components/Cart/Cart.vue"),
        },
        {
            path: '/compare',
            name: 'compare',
            props: true,
            component: () => import("../components/Compare/Compare.vue"),
        },
        {
            path: '/favorite',
            name: 'favorite',
            props: true,
            component: () => import("../components/Favorite/Favorite.vue"),
        },
        {
            path: '/error/404',
            name: '404',
            component: () => import("../components/Errors/404.vue"),
        },
    ]
});