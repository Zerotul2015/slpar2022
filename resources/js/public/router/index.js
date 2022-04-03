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
            component: () => import("../components/Home.vue"),
            meta: {title: 'С легким паром. Торопиться не надо.'}
        },
    ]
});