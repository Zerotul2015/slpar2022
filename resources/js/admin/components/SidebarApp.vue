<template>
  <div class="menu_sidebar">
    <div class="logo">
      <object type="image/svg+xml" data="/build/images/admin/logo.svg">
      </object>
    </div>
    <nav class="navbar">
      <ul class="nav">
        <li class="menu_sidebar__item">
          <router-link to="/banners">Баннеры</router-link>
        </li>
        <li class="menu_sidebar__item menu_sidebar__item_parent">
          <router-link to="/customers">Покупатели</router-link>
          <ul class="menu_sidebar_submenu">
            <li class="menu_sidebar__item">
              <router-link to="/customers">Список покупателей</router-link>
            </li>
            <li class="menu_sidebar__item">
              <hr>
            </li>
            <li class="menu_sidebar__item">
              <router-link to="/customers/company">Контрагенты</router-link>
            </li>
          </ul>
        </li>
        <li class="menu_sidebar__item menu_sidebar__item_parent">
          <a href="/orders">Заказы</a>
          <ul class="menu_sidebar_submenu">
            <li class="menu_sidebar__item">
              <a href="/admin/orders/list-orders">Список заказов</a>
            </li>
            <li class="menu_sidebar__item">
              <hr>
            </li>
            <li class="menu_sidebar__item">
              <a href="/admin/orders/status">Статусы</a>
            </li>
            <li class="menu_sidebar__item">
              <a href="/admin/orders/delivery-methods">Способы доставки</a>
            </li>
            <li class="menu_sidebar__item">
              <a href="/admin/orders/payment-methods">Способы оплаты</a>
            </li>
          </ul>
        </li>
        <li class="menu_sidebar__item">
          <router-link to="/settings/menu-settings">Меню</router-link>
        </li>
        <li class="menu_sidebar__item menu_sidebar__item_parent">
          <router-link to="/settings">Настройки</router-link>
          <ul class="menu_sidebar_submenu">
            <li class="menu_sidebar__item">
              <router-link to="/settings/users">Пользователи</router-link>
            </li>
            <li class="menu_sidebar__item">
              <router-link to="/settings">Настройки</router-link>
            </li>
            <li class="menu_sidebar__item">
              <router-link to="/settings/layouts">Макеты страниц</router-link>
            </li>
            <li class="menu_sidebar__item">
              <router-link to="/settings/notifications">Оповещения</router-link>
            </li>
          </ul>
        </li>
        <li class="menu_sidebar__item">
          <a href="/admin/settings/sections">Разделы</a>
        </li>
        <!--        <li class="menu_sidebar__item">-->
        <!--          <a href="/admin/discounts/discounts-list">Скидки</a>-->
        <!--        </li>-->
        <li class="menu_sidebar__item menu_sidebar__item_parent">
          <router-link to="/pages">Страницы</router-link>
          <ul class="menu_sidebar_submenu">
            <li class="menu_sidebar__item">
              <router-link to="/pages/create/">Создать страницу</router-link>
            </li>
            <li class="menu_sidebar__item">
              <router-link to="/pages/categories">Категории страниц</router-link>
            </li>
            <li class="menu_sidebar__item">
              <router-link to="/pages/">Список страниц</router-link>
            </li>

            <li class="menu_sidebar__item">
              <hr>
            </li>
            <li class="menu_sidebar__item"><a href="/admin/pages/index-page">Главная страница</a></li>
          </ul>
        </li>
        <li class="menu_sidebar__item">
          <router-link to="/bath-style">Стилевые решения</router-link>
        </li>
        <li class="menu_sidebar__item menu_sidebar__item_parent">
          <router-link to="/products">Управление товаром</router-link>
          <ul class="menu_sidebar_submenu">
            <li class="menu_sidebar__item">
              <router-link to="/products">Список товара</router-link>
            </li>
            <li class="menu_sidebar__item">
              <router-link to="/products/create">Добавить товар</router-link>
            </li>
            <li class="menu_sidebar__item">
              <router-link to="/products/categories">Категории</router-link>
            </li>
            <li>
              <hr>
            </li>
            <li class="menu_sidebar__item">
              <router-link to="/products/units">Единицы измерения</router-link>
            </li>
            <li class="menu_sidebar__item"><a href="/admin/products/manufacturers">Производители</a></li>
            <li class="menu_sidebar__item"><a href="/admin/products/stock-status">Статусы наличия</a></li>
            <li class="menu_sidebar__item"><a href="/admin/products/characteristics">Характеристики</a></li>
            <li class="menu_sidebar__item"><a href="/admin/products/label">Ярлыки</a></li>
          </ul>
        </li>
        <li class="menu_sidebar__item">
          <a href="/admin/authorization/logout">Выход</a>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script>
export default {
  name: "SidebarApp"
}
</script>

<style scoped>
.logo {
  display: grid;
  justify-content: center;
}
</style>