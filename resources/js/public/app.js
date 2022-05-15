import Vue from 'vue'

import App from "./components/App.vue"
import router from "./router"
import store from "./store/index"
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'
import axios from "axios";
import SweetModal from "sweet-modal-vue/src/plugin";
import Viewer from 'v-viewer'
// Preferred: as a plugin (directive + filter) + custom placeholders support
import VueMask from 'v-mask'

Vue.use(Viewer)
Vue.use(SweetModal)
Vue.use(VueMask);

Vue.filter('priceToLocale', function (value) {
    if (!value) return '';
    let parseVal = parseInt(value);
    if (isNaN(parseVal)) {
        return value;
    } else {
        return parseVal.toLocaleString('ru');
    }
});


Vue.config.productionTip = false;


router.beforeEach((to, from, next) => {
    if (to.params && to.params.isCustom) {
        location.href = to.path;
    } else {
        next();
    }
});
router.beforeResolve((to, from, next) => {
    NProgress.start();
    console.log('allowAnonymous: ' + to.meta.allowAnonymous);
    console.log('allowCustomer: ' + to.meta.allowCustomer);
    console.log('allowWholesale: ' + to.meta.allowWholesale);
    console.log('isAuth: ' + to.meta.isAuth);
    if(!to.meta.allowAnonymous && ((to.meta.allowCustomer && !app.isAuth) || (to.meta.allowWholesale && !app.isWholesale))) {
        console.log('access restricted');
            next({'name': 'AccessRestricted'})
        //app.$router.push({'name': 'AccessRestricted'});
    }else {
        if (to.name) {
            app.$store.commit('templateData/setSection', to.name);
            if (to.params.url) {
                app.$store.commit('templateData/setSectionKey', to.params.url);
            }

            if (to.name === 'productCategory' && from.name !== 'productCategory') {
                app.$store.dispatch('bathStyle/changeToggleFilterForCategory', false);
                app.$store.dispatch('bathStyle/setActiveStyleKey', 0);
            }
            // Start the route progress bar.
        }
        next();
    }
})

router.afterEach((to, from) => {
    // Complete the animation of the route progress bar.
    NProgress.done();
})

// create a new axios instance
axios.create({
    baseURL: '/api'
})
// before a request is made start the nprogress
axios.interceptors.request.use(config => {
    NProgress.start()
    return config
})

// before a response is returned stop nprogress
axios.interceptors.response.use(response => {
    NProgress.done()
    return response
})


const app = new Vue({
    router,
    store,
    render: h => h(App),
    data() {
        return {
            scrollY: 0,
        }
    },
    created() {
        window.addEventListener('scroll', this.handleScroll);
    },
    destroyed() {
        window.removeEventListener('scroll', this.handleScroll);
    },
    methods: {
        isInViewport(elem) {
            let bounding = elem.getBoundingClientRect();
            return (
                bounding.top >= 0 &&
                bounding.left >= 0 &&
                bounding.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                bounding.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        },
        handleScroll() {
            this.scrollY = window.scrollY;
        },
        guid() {
            return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
                (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16))
        },
        validateMail: function (mailString) {
            let mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            return !!mailString.match(mailformat);
        },
    },
}).$mount('#app')