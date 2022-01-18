<template>
  <div class="wrapper-content">
    <h1>Категории страниц</h1>
    <div class="buttons-block">
      <router-link class="button button_green" to="/pages/categories/create">
        <span class="button-icon"><i class="far fa-plus"></i></span>
        <span class="button-text">Создать категорию</span>
      </router-link>
    </div>
    <div class="form-section">
      <div class="input-block">
        <label for="search-page-input">Искать по названию:</label>
        <input id="search-page-input" class="input" type="text" v-model="searchString">
      </div>
    </div>
    <div v-if="loading" class="loading">
      Загрузка...
    </div>
    <div v-if="error" class="error">
      {{ error }}
    </div>
    <div><span v-if="searchString">Найдено</span><span v-else>Всего</span> категорий: <span
        v-html="elementCount"></span> шт.
    </div>
    <div class="list" v-if="elementCount">
      <div class="pagination" v-if="this.paginationPageCount > 1">
        <div class="pagination-title">Выберите страницу:</div>
        <div class="pagination-link" v-for="n in this.paginationPageCount"
             :class="{'pagination-link-current':(paginationPageCurrent===n)}"
             @click="getCategories(n)">
          {{ n }}
        </div>
      </div>
      <router-link class="list-item" v-for="(categoryItem, categoryKey) in categories.objects" :key="$root.guid()"
                   :to="'/pages/categories/edit/' + categoryItem.id">
        <div>{{ categoryItem.name_short }}</div>
      </router-link>
    </div>
  </div>
</template>

<script>
import api from "../../common/api";
import {debounce} from 'lodash';

export default {
  name: "PagesCategories",
  data() {
    return {
      loading: false,
      error: null,
      categories: [],
      //поиск
      searchString: '',
      //постраничная навигация
      elementCount: 0,
      paginationPerPage: 20,
      paginationPageCurrent: 1,
      //конец навигации по страницам
    }
  },
  mounted() {
    this.getCategories();
  },
  computed: {
    searchArray() {
      let returnArray = {};
      if (this.searchString.length > 1) {
        returnArray = {
          'andWhere': [
            {
              'where': 'name_short',
              'searchString': this.searchString,
              'condition': 'like',
              'group': '0',
              'groupCondition': 'OR',
            },
            {
              'where': 'name_full',
              'searchString': this.searchString,
              'condition': 'like',
              'group': '0',
              'groupCondition': 'OR',
            },
          ]
        }
      }
      return returnArray;
    },
    paginationPageCount() {
      return Math.ceil(this.pagesCount / this.paginationPerPage);
    },
  },
  watch: {
    searchString() {
      this.getCategories();
    },
  },
  methods: {
    getCount() {
      let sendData = {'count': true};
      sendData = Object.assign(sendData, this.searchArray);
      api.getData('pageCategory', sendData)
          .then((r) => {
            this.elementCount = r.returnData ? r.returnData : 0;
            if (r.error) {
              this.error = 'Во время получения количества категорий возникла ошибка: ' + r.error ? r.error : 'неизвестная ошибка';
            }
          })
          .catch((e) => {
            this.error = e.error;
          })
    },
    getCategories: debounce(async function (pageNumber) {
      if (!pageNumber) {
        pageNumber = 1;
      } else {
        this.paginationPageCurrent = pageNumber;
      }
      this.loading = true;
      this.getCount();
      let sendData = {'pagination': {'page': pageNumber, 'perPage': 20}};
      sendData = Object.assign(sendData, this.searchArray);
      api.getData('pageCategory', sendData)
          .then((r) => {
            this.loading = false;
            this.categories = r.returnData ? r.returnData : {};
            if (r.error) {
              this.error = 'Во время получения категорий возникла ошибка: ' + r.error ? r.error : 'неизвестная ошибка';
            }
          })
          .catch((e) => {
            this.loading = false;
            this.error = e.error;
          })
    },1500)
  },
}
</script>

<style scoped>

</style>