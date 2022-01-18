<template>
  <div class="wrapper-content">
    <h1>Страницы</h1>
    <div class="buttons-block">
      <router-link class="button button_green" to="/pages/create">
        <span class="button-icon"><i class="far fa-plus"></i></span>
        <span class="button-text">Создать страницу</span>
      </router-link>
    </div>
    <div class="form-section">
      <div class="input-block">
        <label for="search-page-input">Искать по названию:</label>
        <input id="search-page-input" class="input" type="text" v-model="searchString">
      </div>
    </div>
    <div>
      <span v-if="searchString">Найдено</span><span v-else>Всего</span> страниц: <span v-html="pagesCount"></span>
      шт.
    </div>
    <Pagination :route-prefix="'/pages/page/'" :page-count="this.paginationPageCount"/>
    <div v-if="error" class="error">
      {{ error }}
    </div>
    <LoaderSpinner v-if="isLoadingProducts"></LoaderSpinner>
    <div class="list" v-else>
         <router-link class="list-item" v-for="(pageItem, pageKey) in pages.objects" :key="$root.guid()"
                   :to="'/pages/edit/' + pageItem.id">
        <div>{{ pageItem.title }}</div>
      </router-link>
    </div>
  </div>
</template>

<script>
import api from "../../common/api";
import Pagination from "../Other/Pagination.vue";
import {debounce} from 'lodash';
import LoaderSpinner from "../Other/LoaderSpinner";

export default {
  name: "Pages",
  components: {
    Pagination,
    LoaderSpinner,
  },
  data() {
    return {
      isLoadingProducts:false,
      error: null,
      pages: [],
      //поиск
      searchString: '',
      // навигация по страницам
      pagesCount: 0,
      paginationPerPage: 20,
      // конец навигация по страницам
    }
  },
  mounted() {
    this.getPages();
  },
  watch: {
    pageNumber(){
      this.getPages();
    },
    searchString() {
      this.getPages();
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
      return Math.ceil(this.pagesCount / this.paginationPerPage);
    },
    searchArray() {
      let returnArray = {};
      if (this.searchString.length > 1) {
        returnArray = {
          'andWhere': [
            {
              'where': 'title',
              'searchString': this.searchString,
              'condition': 'like',
              'group': '0',
              'groupCondition': 'AND',
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
      api.getData('page', sendData)
          .then((r) => {
            this.pagesCount = r.returnData ? r.returnData : 0;
            if (r.error) {
              this.error = 'Во время получения количества страниц возникла ошибка: ' + r.error ? r.error : 'неизвестная ошибка';
            }
          })
          .catch((e) => {
            this.error = e.error;
          })
    },
    getPages: debounce(async function () {
      this.isLoadingProducts = true;
      let pageNumber = this.pageNumber ? this.pageNumber : 1;
      this.getCount();
      let sendData = {'pagination': {'page': pageNumber, 'perPage': 20}};
      sendData = Object.assign(sendData, this.searchArray);
      api.getData('page', sendData)
          .then((r) => {
            setTimeout(()=>{this.isLoadingProducts = false}, 1000);
            this.pages = r.returnData ? r.returnData : {};
            if (r.error) {
              this.error = 'Во время получения страниц возникла ошибка: ' + r.error ? r.error : 'неизвестная ошибка';
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
</style>