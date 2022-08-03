<template>
  <header class="header-page"
          :class="{'header-page_fixed':headerFixed || styleHeaderToggle, 'header-page_mobile':isMobile, 'header-page_tablet':isTablet,
          'header-page_bath-page':(sectionSite==='index' || sectionSite ==='bathStyle')}">
    <div class="header-wrap">
      <div class="header-content"
           :class="{'header-content_mobile':isMobile,'header-content_tablet':isTablet,
           'header-content_bath-page':(sectionSite==='index' || sectionSite ==='bathStyle')}">
        <a class="header-logo" :class="{'header-logo_mobile':isMobile, 'header-logo_tablet':isTablet}" href="/">
          <img src="/build/images/logo_yellow.svg" alt="С легким паром!">
        </a>
        <div class="header-catalog" :class="{'header-catalog_mobile':isMobile,'header-catalog_tablet':isTablet}">
          <div class="h-catalog-link h-link btn btn_header" @click="menuCatalogIsOpen = !menuCatalogIsOpen">
            <icon-svg class="btn-icon" :icon="iconCatalog"></icon-svg>
            <span class="btn-text" v-if="!isMobile">Каталог</span>
          </div>
          <div class="h-catalog"
               :class="{'h-catalog_mobile':isMobile,'h-catalog_tablet':isTablet,'h-catalog-open':menuCatalogIsOpen && isMobile}">
            <div class="h-catalog-group">
              <router-link class="h-catalog-link-main" :to="'/bath-style/'">Комплексные стилевые решения</router-link>
              <div class="h-catalog-child-group" v-if="bathStyles">
                <router-link class="h-catalog-link-child" v-for="(styleItem, keyStyle) in bathStyles"
                             :key="$root.guid()"
                             :to="'/bath-style/' + styleItem.url">
                  <span class="h-icon-link-child"><i class="far fa-chevron-right"></i></span>
                  <span class="h-text-link-child">{{ styleItem.name }}</span>
                </router-link>
              </div>
            </div>
            <div class="h-catalog-group" v-for="(categoryMain, keyGroup) in catalog[0]">
              <router-link class="h-catalog-link-main" :to="'/catalog/'+ categoryMain.url">
                {{ categoryMain.name }}
              </router-link>
              <div class="h-catalog-child-group" v-if="catalog[categoryMain.id]">
                <router-link class="h-catalog-link-child"
                             v-for="(categoryChild, keyChildCat) in catalog[categoryMain.id]"
                             :key="$root.guid()"
                             :to="'/catalog/' + categoryMain.url + '/' + categoryChild.url">
                  <span class="h-icon-link-child"><i class="far fa-chevron-right"></i></span>
                  <span class="h-text-link-child">{{ categoryChild.name }}</span>
                </router-link>
              </div>
            </div>
          </div>
        </div>
        <div class="header-links-block" v-if="isMobile === false && isTablet === false">
          <div class="hlb-links">
            <router-link v-if="isAuth" class="header-link-order-form" to="/dealer/order-form">Бланк заказа</router-link>
            <router-link class="h-link" v-for="(menuItem) in menuHeader"
                         :key="$root.guid()"
                         :to="{path:menuItem.value, params:{isCustom:(menuItem.typeItem === 'custom')}}">
              {{ menuItem.title }}
            </router-link>
          </div>
        </div>
        <div class="header-toggle-style">
          <div @click="styleHeaderToggleChange(!styleHeaderToggle)" class="header-toggle-style-text-label"
          :class="{'header-toggle-style-text-label-active':styleHeaderToggle}">Выбрать стиль</div>
          <ElementsToggleSwitch :is-checked="styleHeaderToggle" @changeToggle="styleHeaderToggleChange"/>
        </div>
        <search-site-mobile v-if="isMobile"/>
        <search-site class="header-search-block" v-else
                     :iconShow="true" custom-class="hs-block"/>
        <div class="header-icon-block">
          <router-link class="header-link-enter" :to="linkCabinet" title="Вход">
            <icon-svg class="header-icon-cart" icon="user-large"></icon-svg>
          </router-link>
          <router-link to="/compare" div class="header-icon-compare-wrap">
            <icon-svg class="header-icon-compare" :class="{'header-icon-compare-full':compareCount}"
                      :scaleX="2" :icon="compareIcon"/>
          </router-link>
          <router-link to="/cart" class="header-icon-cart-wrap" :class="{'header-icon-label-full':cartCount}">
            <icon-svg class="header-icon-cart" icon="cart-header"/>
          </router-link>
        </div>
      </div>
    </div>
    <div v-if="headerFixed && (sectionSite==='index' || sectionSite ==='bathStyle')" class="h-fixed-second-line">
      <BathStyleBinding/>
    </div>
    <div v-if="styleHeaderToggle" class="h-fixed-second-line">
      <HeaderStyleCarouselNavigate class="hcs-wrapper_bath-or-index"/>
    </div>
<!--    <div v-if="headerFixed && (sectionSite==='index' || sectionSite ==='bathStyle')" class="h-fixed-second-line">-->
<!--      <HeaderStyleCarouselNavigate class="hcs-wrapper_bath-or-index"/>-->
<!--    </div>-->
  </header>
</template>

<script>
import VueHorizontal from "vue-horizontal";
import IconSvg from "./Icon-svg/icon-svg";
import SearchSite from "./search/SearchSite";
import SearchSiteMobile from "./search/SearchSiteMobile";
import HeaderStyleCarouselNavigate from "./Header/HeaderStyleCarouselNavigate";
import BathStyleBinding from "./BathStyle/BathStyleBinding";
import ElementsToggleSwitch from "./Elements/ElementsToggleSwitch";

export default {
  name: "HeaderApp",
  components: {
    ElementsToggleSwitch,
    BathStyleBinding,
    HeaderStyleCarouselNavigate,
    SearchSiteMobile, SearchSite, IconSvg, VueHorizontal
  },
  data() {
    return {
      menuNav: [],
      menuCatalogIsOpen: false,
      styleHeaderToggleTemp:false,
    }
  },
  watch: {
    selectBathStyleIndex(newVal) {
      if (newVal === this.$refs) {

      }
    },
    menuCatalogIsOpen() {
    },
    sectionSite() {
      this.menuCatalogIsOpen = false
    },
    sectionSiteKey() {
      this.menuCatalogIsOpen = false
    }
  },
  computed: {
    styleHeaderToggle(){
      return this.$store.getters["bathStyle/styleHeaderToggle"];
    },
    iconCatalog() {
      let iconName = 'bars';
      if (this.isMobile === true && this.menuCatalogIsOpen === true) {
        iconName = 'xmark';
      }
      return iconName;
    },
    isAuth() {
      return this.$store.getters["customer/isAuth"];
    },
    isMobile() {
      return this.$store.getters['templateData/isMobile'];
    },
    isTablet() {
      return this.$store.getters['templateData/isTablet'];
    },
    isWholesale() {
      return this.$store.getters["customer/isWholesale"];
    },
    linkCabinet() {
      return this.isWholesale ? '/customer/wholesale-profile' : this.isAuth ? '/customer/profile' : '/customer/home';
    },
    carouselActive() {
      let siteSectionUsed = ['productCategory', 'productCategoryWithStyle', 'compare', 'bathStyle', 'index'];
      return siteSectionUsed.includes(this.sectionSite);
    },
    sectionSite() {
      return this.$store.getters["templateData/section"];
    },
    sectionSiteKey() {
      return this.$store.getters["templateData/sectionKey"];
    },
    headerFixed() {
      return this.$root.scrollY > 0;
    },
    catalog() {
      return this.$store.getters["templateData/menuCatalog"];
    },
    menuHeader() {
      return this.$store.getters["templateData/menuHeader"];
    },
    bathStyles() {
      return this.$store.getters["bathStyle/all"];
    },
    cartCount() {
      return this.$store.getters['cart/count'];
    },
    compareCount() {
      return this.$store.getters['compare/count'];
    },
    compareIcon() {
      let iconName = 'compare-empty';
      if (this.compareCount) {
        iconName = 'compare-full';
      }
      return iconName;
    }
  },
  methods: {
    styleHeaderToggleChange(newStateToggle){
      this.$store.dispatch('bathStyle/changeStyleHeaderToggle', newStateToggle)
    }
  },
}
</script>

<style scoped>


.hlb-menu-style >>> .v-hl-btn-prev {
  background: linear-gradient(to left, #4f4f4f00 0, #4f4f4f 66%, #4f4f4f);
  padding-right: 1rem;
  padding-left: 3rem;
}

.hlb-menu-style >>> .v-hl-btn-next {
  background: linear-gradient(to right, #4f4f4f00 0, #4f4f4f 66%, #4f4f4f);
  padding-left: 1rem;
  padding-right: 3rem;
}
</style>