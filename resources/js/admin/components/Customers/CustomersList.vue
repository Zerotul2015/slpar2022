<template>
  <div class="wrapper-content">
    <h1 v-html="typeCustomerView">Покупатели</h1>
    <div class="buttons-block">
      <router-link class="button button_green" to="/customers/create">
        <span class="button-icon"><i class="far fa-user-plus"></i></span>
        <span class="button-text">Добавить покупателя</span>
      </router-link>
    </div>
    <div class="form-section">
      <button class="button" @click="filterWholesale = true">Оптовые покупатели</button>
      <button class="button" @click="filterWholesale = false" >Обычные покупатели</button>
    </div>
    <div class="form-section">
      <div class="input-block">
        <label for="search-page-input">Поиск покупателей:</label>
        <input id="search-page-input" class="input" type="text" v-model="searchString">
      </div>
    </div>
    <div><span v-if="searchString">Найдено</span><span v-else>Всего</span> покупателей: <span
        v-html="itemsCount"></span>
      шт.
    </div>
    <div class="pagination" v-if="this.paginationPageCount > 1">
      <div class="pagination-title">Страница:</div>
      <router-link class="pagination-link" v-for="n in this.paginationPageCount"
                   :class="{'pagination-link-current':(pageNumber===n)}"
                   :to="'/customers/page/'+ n"
                   :key="$root.guid()">
        {{ n }}
      </router-link>
    </div>
    <div v-if="error" class="error">
      {{ error }}
    </div>
    <div class="grid background-alternation" v-if="itemsCount">
      <CustomersListItem class="row" v-for="(currentItem, keyItem) in items.objects"
                         :item="currentItem"
                         :key="$root.guid()"
                         v-on:item-removed="items.objects.splice(keyItem, 1)">
      </CustomersListItem>
    </div>
    <div class="pagination" v-if="this.paginationPageCount > 1">
      <div class="pagination-title">Страницу:</div>
      <router-link class="pagination-link" v-for="n in this.paginationPageCount"
                   :class="{'pagination-link-current':(pageNumber===n)}"
                   :to="'/customers/page/'+ n"
                   :key="$root.guid()">
        {{ n }}
      </router-link>

    </div>
  </div>
</template>

<script>
import CustomersListItem from "./CustomersListItem.vue";
import api from "../../common/api";
import {debounce} from "lodash";

export default {
  name: "CustomersList",
  components: {
    CustomersListItem,
  },
  props: {
    pageNumber: {
      required: false,
    }
  },
  data() {
    return {
      isLoading: false,
      error: null,
      items: [],
      filterWholesale:false,
      //поиск
      searchString: '',
      // навигация по страницам
      itemsCount: 0,
      paginationPerPage: 20,
      // конец навигация по страницам
    }
  },
  mounted() {
    this.getItems();
  },
  watch:{
    filterWholesale(){
      this.getItems();
    }
  },
  computed: {
    typeCustomerView(){
      let text = 'Покупатели';
      if (this.filterWholesale === true){
        text = 'Оптовые покупатели';
      }
      return text;
    },
    paginationPageCount() {
      return Math.ceil(this.itemsCount / this.paginationPerPage);
    },
    searchArray() {
      let returnArray = {
        'andWhere': [
          {
            'where': 'deleted',
            'searchString': 0,
            'condition': '=',
            'group': '0',
            'groupCondition': 'AND',
          },
          {
            'where': 'is_wholesale',
            'searchString': 1,
            'condition': this.filterWholesale ? '=' : '<>',
            'group': '0',
            'groupCondition': 'AND',
          }
        ]
      };
      if (this.searchString.length > 1) {
        returnArray['andWhere'].push({
          'where': 'name',
          'searchString': this.searchString,
          'condition': 'like',
          'group': '0',
          'groupCondition': 'AND',
        });
      }
      return returnArray;
    },
  },
  methods: {
    getCount() {
      let sendData = {'count': true};
      sendData = Object.assign(sendData, this.searchArray);
      api.getData('customer', sendData)
          .then((r) => {
            this.itemsCount = r.returnData ? r.returnData : 0;
            if (r.error) {
              this.error = 'Во время получения количества покупателей возникла ошибка: ' + r.error ? r.error : 'неизвестная ошибка';
            }
          })
          .catch((e) => {
            this.error = e.error;
          })
    },
    getItems: debounce(async function () {
      this.isLoading = true;
      this.products = {};
      let pageNumber = this.pageNumber ? this.pageNumber : 1;
      this.getCount();
      let sendData = {'pagination': {'page': pageNumber, 'perPage': 20}};
      sendData = Object.assign(sendData, this.searchArray);
      api.getData('customer', sendData)
          .then((r) => {
            this.isLoading = false;
            this.items = r.returnData ? r.returnData : {};
            if (r.error) {
              this.error = 'Во время получения покупателей возникла ошибка: ' + r.error ? r.error : 'неизвестная ошибка';
            }
          })
          .catch((e) => {
            this.isLoading = false;
            this.error = e.error;
          })
    }, 1500)
  }
}
</script>

<style scoped>
</style>