import Vue from "vue";
import Router from "vue-router";

Vue.use(Router);

export default new Router({
    mode: 'history',
    base: '/',
    routes: [
        {
            path: '/',
            name: 'Home',
            component: () => import("../components/BathStyle/BathStyle.vue"),
        },
        {
            path: '/catalog/:url',
            name: 'category',
            props: true,
            component: () => import("../components/Category/CategoryPage.vue"),
        },
        {
            path: '/catalog/*/:url',
            name: 'subCategory',
            props: true,
            component: () => import("../components/Category/CategoryPage.vue"),
        },,
        {
            path: '/bath-style/:url',
            name: 'bathStyle',
            props: true,
            component: () => import("../components/BathStyle/BathStyle.vue"),
        },
    ]
});