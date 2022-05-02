<template>
  <div class="wrapper-content">
    <h1>Промо-коды</h1>
    <div class="buttons-block">
      <router-link class="button button_green" to="/promo-code/create">
        <span class="button-icon"><i class="far fa-plus"></i></span>
        <span class="button-text">Создать промо-код</span>
      </router-link>
    </div>
    <div class="form-section">
      <div class="input-block">
        <label for="search-page-input">Искать:</label>
        <input id="search-page-input" class="input" type="text" v-model="searchString">
      </div>
    </div>
    <div>
      <span v-if="searchString">Найдено</span><span v-else>Всего</span> страниц: <span v-html="pagesCount"></span>
      шт.
    </div>
    <Pagination :route-prefix="'/promo-code/page/'" :page-count="this.paginationPageCount"/>
    <div v-if="error" class="error">
      {{ error }}
    </div>
    <LoaderSpinner v-if="isLoadingItems"></LoaderSpinner>
    <div class="list" v-else>
      <router-link class="list-item" v-for="(item, pageKey) in items.objects" :key="$root.guid()"
                   :to="'/promo-code/' + item.id">
        <div>{{ item.code_text }}</div>
      </router-link>
    </div>
  </div>
</template>

<script>
import Pagination from "../Other/Pagination";
import LoaderSpinner from "../Other/LoaderSpinner";
import api from "../../common/api";
import {debounce} from "lodash";

export default {
  name: "PromoCodeList",
  components: {
    Pagination,
    LoaderSpinner,
  },
  data() {
    return {
      isLoadingItems: false,
      error: null,
      items: [],
      //поиск
      searchString: '',
      // навигация по страницам
      pagesCount: 0,
      paginationPerPage: 20,
      // конец навигация по страницам
    }
  },
  mounted() {
    this.getItems();
  },
  watch: {
    pageNumber(){
      this.getItems();
    },
    searchString() {
      this.getItems();
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
              'where': 'code_text',
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
      api.getData('promoCode', sendData)
          .then((r) => {
            this.pagesCount = r.returnData ? r.returnData : 0;
            if (r.error) {
              this.error = 'Во время получения данных о к-во возникла ошибка: ' + r.error ? r.error : 'неизвестная ошибка';
            }
          })
          .catch((e) => {
            this.error = e.error;
          })
    },
    getItems: debounce(async function () {
      this.isLoadingItems = true;
      let pageNumber = this.pageNumber ? this.pageNumber : 1;
      this.getCount();
      let sendData = {'pagination': {'page': pageNumber, 'perPage': 20}};
      sendData = Object.assign(sendData, this.searchArray);
      api.getData('promoCode', sendData)
          .then((r) => {
            setTimeout(()=>{this.isLoadingItems = false}, 1000);
            this.items = r.returnData ? r.returnData : {};
            if (r.error) {
              this.error = 'Во время получения данных возникла ошибка: ' + r.error ? r.error : 'неизвестная ошибка';
            }
          })
          .catch((e) => {
            setTimeout(()=>{this.isLoadingItems = false}, 1500);
            this.error = e.error;
          })
    }, 1500)
  }
}
</script>

<style scoped>

</style>