import Vue from "vue";
import Router from "vue-router";

Vue.use(Router);

export default new Router({
    mode: 'history',
    base: '/admin/',
    routes: [
        {
            path: '/',
            name: 'Home',
            component: () => import("../components/Home.vue"),
            meta: {title: 'Панель управления'}
        },
        {
            path: '/login',
            name: 'Login',
            component: () => import("../components/Login.vue"),
            meta: {title: 'Вход в панель управления'}
        },
        {
            path: '/banners',
            name: 'Banners',
            component: () => import("../components/Settings/settingsBanners.vue"),
            meta: {title: 'Баннеры - Панель управления'}
        },
        {
            path: '/settings/users',
            name: 'Users',
            component: () => import("../components/Settings/SettingsUsers.vue"),
            meta: {title: 'Пользователи - Панель управления'}
        },
        {
            path: '/settings',
            name: 'Settings',
            component: () => import("../components/Settings/Settings.vue"),
            meta: {title: 'Настройки магазина - Панель управления'}
        },
        {
            path: '/settings/layouts',
            name: 'SettingsLayouts',
            component: () => import("../components/Settings/SettingsLayouts.vue"),
            meta: {title: 'Макеты страниц - Панель управления'}
        },
        {
            path: '/settings/notifications',
            name: 'SettingsNotifications',
            component: () => import("../components/Settings/SettingsNotifications.vue"),
            meta: {title: 'Оповещения настройки - Панель управления'}
        },
        {
            path: '/settings/menu-settings',
            name: 'SettingsMenu',
            component: () => import("../components/MenuSettings/MenuSettings.vue"),
            meta: {title: 'Меню - Панель управления'}
        },
        //---Страницы---Страницы---Страницы---Страницы---Страницы---Страницы---Страницы---//
        {
            path: '/pages/page/:pageNumber',
            props: true,
            name: 'PagesPage',
            component: () => import("../components/Pages/Pages.vue"),
            meta: {title: 'Страницы - Панель управления'}
        },
        {
            path: '/pages',
            name: 'Pages',
            component: () => import("../components/Pages/Pages.vue"),
            meta: {title: 'Страницы - Панель управления'}
        },
        {
            path: '/pages/edit/:id',
            name: 'PagesEdit',
            props: true,
            component: () => import("../components/Pages/PagesEdit.vue"),
            meta: {title: "Редактироване страницы :id - Панель управления"}
        },
        {
            path: '/pages/create',
            name: 'PagesCreate',
            component: () => import("../components/Pages/PagesEdit.vue"),
            meta: {title: 'Создание Страницы - Панель управления'}
        },
        {
            path: '/pages/categories/',
            name: 'PagesCategories',
            component: () => import("../components/Pages/PagesCategories.vue"),
            meta: {title: 'Категории страниц - Панель управления'}
        },
        {
            path: '/pages/categories/create',
            name: 'PagesCategoriesCreate',
            component: () => import("../components/Pages/PagesCategoriesCreate.vue"),
            meta: {title: 'Создание категории страниц - Панель управления'}
        },
        {
            path: '/pages/categories/edit/:id',
            name: 'PagesCategoriesEdit',
            props: true,
            component: () => import("../components/Pages/PagesCategoriesCreate.vue"),
            meta: {title: 'Редактирование категории страниц - Панель управления'}
        },
        //---БанныеСтили---БанныеСтили---БанныеСтили---БанныеСтили---БанныеСтили---БанныеСтили---БанныеСтили---//
        {
            path: '/bath-style/page/:pageNumber',
            props: true,
            name: 'BathStyleListPage',
            component: () => import("../components/BathStyle/BathStyleList.vue"),
            meta: {title: `Стилевые решения для бань - Панель управления`}
        },
        {
            path: '/bath-style',
            name: 'BathStyleList',
            component: () => import("../components/BathStyle/BathStyleList.vue"),
            meta: {title: 'Стилевые решения для бань - Панель управления'}
        },
        {
            path: '/bath-style/create',
            name: 'BathStyleCreate',
            component: () => import("../components/BathStyle/BathStyleCreate.vue"),
            meta: {title: 'Создание стиля - Панель управления'}
        },
        {
            path: '/bath-style/edit/:id',
            props: true,
            name: 'BathStyleEdit',
            component: () => import("../components/BathStyle/BathStyleCreate.vue"),
            meta: {title: 'Редактирование стиля - Панель управления'}
        },
        //---Товары---Товары---Товары---Товары---Товары---Товары---Товары---Товары---//
        {
            path: '/products/page/:pageNumber',
            props: true,
            name: 'ProductsListPage',
            component: () => import("../components/Products/ProductsList.vue"),
            meta: {title: `Список товаров - Панель управления`}
        },
        {
            path: '/products',
            name: 'ProductsList',
            component: () => import("../components/Products/ProductsList.vue"),
            meta: {title: 'Список товаров - Панель управления'}
        },
        {
            path: '/products/edit/:id',
            props: true,
            name: 'ProductsEdit',
            component: () => import("../components/Products/ProductsCreate.vue"),
            meta: {title: 'Редактирование товара - Панель управления'}
        },
        {
            path: '/products/copy/:idCopy',
            props: true,
            name: 'ProductsCopy',
            component: () => import("../components/Products/ProductsCreate.vue"),
            meta: {title: 'Создание товара из копии - Панель управления'}
        },
        {
            path: '/products/create',
            name: 'ProductsCreate',
            component: () => import("../components/Products/ProductsCreate.vue"),
            meta: {title: 'Создание товара - Панель управления'}
        },
        {
            path: '/products/categories',
            name: 'ProductsCategories',
            component: () => import("../components/Products/ProductsCategories.vue"),
            meta: {title: 'Список категорий товаров - Панель управления'},
        },
        {
            path: '/products/categories/:parentId',
            name: 'ProductsCategoriesChild',
            props: true,
            component: () => import("../components/Products/ProductsCategories.vue"),
            meta: {title: 'Подкатегории товаров - Панель управления'},
        },
        {
            path: '/products/manufacturers',
            name: 'ProductsManufacturers',
            props: true,
            component: () => import("../components/Products/ProductsManufacturers.vue"),
            meta: {title: 'Производители товаров - Панель управления'},
        },
        {
            path: '/products/manufacturers/:id',
            name: 'ProductsManufacturersItem',
            props: true,
            component: () => import("../components/Products/ProductsManufacturersItem.vue"),
            meta: {title: 'Редактирование производителя товаров - Панель управления'},
        },
        {
            path: '/products/stock-status',
            name: 'ProductsStockStatus.vue',
            props: true,
            component: () => import("../components/Products/ProductsStockStatus.vue"),
            meta: {title: 'Статусы наличия товаров - Панель управления'},
        },
        {
            path: '/products/units',
            name: 'ProductsUnits.vue',
            props: true,
            component: () => import("../components/Products/ProductsUnits.vue"),
            meta: {title: 'Единицы измерения товаров - Панель управления'},
        },
        //---Покупатели---Покупатели---Покупатели---Покупатели---Покупатели---Покупатели---//
        {
            path: '/customers/page/:pageNumber',
            props: true,
            name: 'CustomersListPage',
            component: () => import("../components/Customers/CustomersList.vue"),
            meta: {title: `Покупатели страница №:pageNumber - Панель управления`}
        },
        {
            path: '/customers',
            name: 'CustomersList',
            component: () => import("../components/Customers/CustomersList.vue"),
            meta: {title: 'Покупатели - Панель управления'}
        },
        {
            path: '/customers/create/',
            name: 'CustomerCreate',
            props: {customerCreate: true},
            component: () => import("../components/Customers/CustomersItemDetails.vue"),
            meta: {title: 'Создание покупателя - Панель управления'}
        },
        {
            path: '/customers/details/:customerId',
            name: 'CustomerDetails',
            props: true,
            component: () => import("../components/Customers/CustomersItemDetails.vue"),
            meta: {title: 'Редактирование покупателя :customerId - Панель управления'}
        },
        {
            path: '/customers/details/:customerId/company',
            name: 'CustomerCompany',
            props: true,
            component: () => import("../components/Customers/CustomersItemCompany.vue"),
            meta: {title: 'Контрагенты покупателя :customerId - Панель управления'}
        },
        {
            path: '/customers/companies/:customerId',
            name: 'CustomersCompanies',
            props: true,
            component: () => import("../components/Customers/CustomersItemOrders.vue"),
            meta: {title: 'Заказы покупателя :customerId - Панель управления'}
        },
        //---Заказы---Заказы---Заказы---Заказы---Заказы---Заказы---Заказы---Заказы---Заказы---Заказы---//
        {
            path: '/orders',
            name: 'OrdersList',
            component: () => import("../components/Orders/OrdersList.vue"),
            meta: {title: 'Заказы - Панель управления'}
        },
        {
            path: '/orders/details/:orderId',
            name: 'OrderDetails',
            props: true,
            component: () => import("../components/Orders/OrdersItemDetails.vue"),
            meta: {title: 'Заказ №:orderId - Панель управления'}
        },
        {
            path: '/orders/create',
            name: 'OrderCreate',
            component: () => import("../components/Orders/OrdersItemDetails.vue"),
            meta: {title: 'Создание заказа - Панель управления'}
        },
        {
            path: '/orders/page/:pageNumber',
            name: 'OrdersListPage',
            props: true,
            component: () => import("../components/Orders/OrdersList.vue"),
            meta: {title: 'Заказы страница №:pageNumber - Панель управления'}
        },
        {
            path: '/orders/customer/:customerId',
            name: 'OrdersCustomer',
            props: true,
            component: () => import("../components/Orders/OrdersList.vue"),
            meta: {title: 'Заказы покупателя :customerId - Панель управления'}
        },
        {
            path: '/orders/company/:companyId',
            name: 'OrdersCompany',
            props: true,
            component: () => import("../components/Orders/OrdersList.vue"),
            meta: {title: 'Заказы контрагенты :companyId - Панель управления'}
        },
        //---Скидки---Скидки---Скидки---Скидки---Скидки---Скидки---Скидки---Скидки---Скидки---Скидки---//
        {
            path: '/discount',
            name: 'Discount',
            component: () => import("../components/Discount/DiscountList.vue"),
            meta: {title: 'Скидки - Панель управления'}
        },
        {
            path: '/discount/page/:pageNumber',
            name: 'DiscountPage',
            props: true,
            component: () => import("../components/Discount/DiscountList.vue"),
            meta: {title: 'Скидки - Панель управления'}
        },
        {
            path: '/discount/create',
            name: 'DiscountCreate',
            component: () => import("../components/Discount/DiscountCreate.vue"),
            meta: {title: 'Новая скидка - Панель управления'}
        },
        {
            path: '/discount/:itemId',
            name: 'DiscountDetails',
            props: true,
            component: () => import("../components/Discount/DiscountCreate.vue"),
            meta: {title: 'Скидка :discountId - Панель управления'}
        },
        //---Промокод---Промокод---Промокод---Промокод---Промокод---Промокод---Промокод---Промокод---Промокод---//
        {
            path: '/promo-code/create',
            name: 'PromoCodeCreate',
            component: () => import("../components/PromoCode/PromoCode.vue"),
            meta: {title: 'Создание промо-кода - Панель управления'}
        },
        {
            path: '/promo-code/:itemId',
            name: 'PromoCode',
            props: true,
            component: () => import("../components/PromoCode/PromoCode.vue"),
            meta: {title: 'Промо-код :itemId - Панель управления'}
        },
        {
            path: '/promo-code',
            name: 'PromoCodeList',
            component: () => import("../components/PromoCode/PromoCodeList.vue"),
            meta: {title: 'Промо-коды - Панель управления'}
        },
        {
            path: '/promo-code/page/:pageNumber',
            name: 'PromoCodeListPage',
            props: true,
            component: () => import("../components/PromoCode/PromoCodeList.vue"),
            meta: {title: 'Промо-коды - Панель управления'}
        },
    ]
});