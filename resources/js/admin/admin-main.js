import "../../scss/admin/admin.scss"

import Vue from 'vue'

import App from "./components/App.vue"
import router from "./router"
import store from "./store/index"
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'
import axios from "axios";
import authApi from "./common/authApi"
import SweetModal from "sweet-modal-vue/src/plugin";
// Preferred: as a plugin (directive + filter) + custom placeholders support
import VueMask from 'v-mask'

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
    authApi.check().then((isAuthenticated)=>{
        document.title = to.meta.title || 'Панель управления';
        if(to.name !== 'Login' && !isAuthenticated) {
            next({name:'Login'});
        }
        else{
            next();
        }
    })
});
router.beforeResolve((to, from, next)=>{
    if (to.name) {
        // Start the route progress bar.
        NProgress.start();
    }
    next()
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
    data(){
        return{
            //tinymce
            'TINY_API_KEY': "jn70abbhp1e47ehum3uvvmd1iy75zb9n5pgbomwy21ha0vk4",
            configEditor: {
                //content_css : '/Views/Visitors/bootstrap/css/bootstrap.min.css,/Views/Visitors/style/style.min.css',
                height: 600,
                width:'80%',
                language_url: '/build/js/tinymce/langs/ru.js',
                language: 'ru',
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table paste code help wordcount '
                ],
                external_filemanager_path: "/filemanager/",
                filemanager_title: "Менеджер файлов",
                external_plugins: {"filemanager": "/filemanager/plugin.min.js"},
                toolbar:
                    'responsivefilemanager |  undo redo | formatselect | bold italic forecolor backcolor | \
                    alignleft aligncenter alignright alignjustify | \
                    bullist numlist outdent indent | removeformat | help',
                style_formats_merge: true,
                // style_formats: [{
                //     title: 'Кнопка ссылка',
                //     block: 'a',
                //     classes: 'btn btn-sm btn-orange',
                // },]
            },
            //end tinymce
        }
    },
    methods:{
        guid() {
            return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
                (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16))
        },
    },
}).$mount('#app')