<template>
  <div class="wrapper-content">
    <h1>Товары</h1>
    <div class="buttons-block">
      <router-link class="button button_green" to="/products/create">
        <span class="button-icon"><i class="far fa-plus"></i></span>
        <span class="button-text">Создать товар</span>
      </router-link>
    </div>
    <div class="form-section">
      <div class="input-block">
        <label for="search-page-input">Искать по названию или артикулу:</label>
        <input id="search-page-input" class="input" type="text" v-model="searchString">
      </div>
    </div>
    <div><span v-if="searchString">Найдено</span><span v-else>Всего</span> товаров: <span v-html="itemsCount"></span>
      шт.
    </div>
    <Pagination :route-prefix="'/products/page/'" :page-count="this.paginationPageCount"/>
    <div v-if="error" class="error">
      {{ error }}
    </div>
    <LoaderSpinner v-if="isLoadingProducts"></LoaderSpinner>
    <div v-else class="products-grid background-alternation">
      <ProductsListItem class="row" v-for="(currentItem, keyItem) in products.objects"
                        :item="currentItem"
                        :key="$root.guid()"
                        v-on:item-removed="products.objects.splice(keyItem, 1)">
      </ProductsListItem>
    </div>
    <Pagination :route-prefix="'/products/page/'" :page-count="this.paginationPageCount"/>
  </div>
</template>

<script>
import api from "../../common/api";
import ProductsListItem from "./ProductsListItem.vue";
import Pagination from "../Other/Pagination.vue";
import LoaderSpinner from "../Other/LoaderSpinner";
import {debounce} from 'lodash';

export default {
  name: "ProductsList",
  components: {
    ProductsListItem,
    LoaderSpinner,
    Pagination
  },
  data() {
    return {
      isLoading: false,
      isLoadingProducts: false,
      error: null,
      products: [],
      //поиск
      searchString: '',
      // навигация по страницам
      itemsCount: 0,
      paginationPerPage: 20,
      // конец навигация по страницам
    }
  },
  mounted() {
    this.getCount();
    this.$store.dispatch('productCategory/getAllById');//получаем данные категорий товаров
    this.$store.dispatch('productStockStatus/getAllById');//получаем данные категорий товаров
  },
  watch: {
    pageNumber() {
      this.getProducts();
    },
    searchString(newVal, oldVal) {
      if (newVal !== oldVal) {
        this.$router.push({name: 'ProductsListPage', params: {'pageNumber': 1}});
      }
      this.getProducts();
    },
  },
  computed: {
    pageNumber() {
      let pageNumber = this.$route.params.pageNumber ? parseInt(this.$route.params.pageNumber) : 1;
      pageNumber = isNaN(pageNumber) || pageNumber === 0 ? 1 : pageNumber;
      pageNumber = pageNumber >= this.paginationPageCount ? this.paginationPageCount : pageNumber;
      return pageNumber;
    },
    paginationPageCount() {
      return Math.ceil(this.itemsCount / this.paginationPerPage);
    },
    categories() {
      return this.$store.state.productCategory.allById;
    },
    searchArray() {
      let returnArray = {};
      if (this.searchString.length > 1) {
        returnArray = {
          'andWhere': [
            {
              'where': 'name',
              'searchString': this.searchString,
              'condition': 'like',
              'group': '0',
              'groupCondition': 'OR',
            },
            {
              'where': 'article',
              'searchString': this.searchString,
              'condition': 'like',
              'group': '0',
              'groupCondition': 'OR',
            }
          ]
        }
      }
      return returnArray;
    },

  },
  methods: {
    getCount() {
      let sendData = {'count': true};
      sendData = Object.assign(sendData, this.searchArray);
      api.getData('product', sendData)
          .then((r) => {
            this.itemsCount = r.returnData ? r.returnData : 0;
            if (r.error) {
              this.error = 'Во время получения количества страниц возникла ошибка: ' + r.error ? r.error : 'неизвестная ошибка';
            }
          })
          .catch((e) => {
            this.error = e.error;
          })
    },
    getProducts: debounce(async function () {
      this.isLoadingProducts = true;
      this.products = {};
      let pageNumber = this.pageNumber ? this.pageNumber : 1;
      this.getCount();
      let sendData = {'pagination': {'page': pageNumber, 'perPage': 20}};
      sendData = Object.assign(sendData, this.searchArray);
      api.getData('product', sendData)
          .then((r) => {
            setTimeout(()=>{this.isLoadingProducts = false}, 1000);
            setTimeout(()=>this.products = r.returnData ? r.returnData : {}, 1000);
            if (r.error) {
              this.error = 'Во время получения товаров возникла ошибка: ' + r.error ? r.error : 'неизвестная ошибка';
            }
          })
          .catch((e) => {
            setTimeout(()=>{this.isLoadingProducts = false}, 1500);
            this.error = e.error;
          })
    }, 1500)
  }
}
</script>

<style scoped>
.products-grid {
  display: flex;
  flex-direction: column;
  flex-wrap: wrap;
}


</style>