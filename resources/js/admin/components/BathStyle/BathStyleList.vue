<template>
  <div class="wrapper-content">
    <h1>Стилевые решения для бань и саун</h1>
    <div class="buttons-block">
      <button class="button button_green" @click="addBathStyle">
        <span class="button-icon"><i class="far fa-plus"></i></span>
        <span class="button-text">Создать стиль</span>
      </button>
    </div>
    <div class="form-section">
      <div class="input-block">
        <label for="search-page-input">Искать по названию:</label>
        <input id="search-page-input" class="input" type="text" v-model="searchString">
      </div>
    </div>
    <div><span v-if="searchString">Найдено</span><span v-else>Всего</span> стилей: <span v-html="itemsCount"></span>
      шт.
    </div>
    <Pagination :route-prefix="'/bath-style/page/'" :page-count="this.paginationPageCount"/>
    <div v-if="error" class="error">
      {{ error }}
    </div>
    <LoaderSpinner v-if="isLoadingData"></LoaderSpinner>
    <div v-else class="products-grid background-alternation">
      <BathStyleListItem class="row" v-for="(currentItem, keyItem) in itemsData.objects"
                         :item="currentItem"
                         :key="$root.guid()"
                         v-on:item-removed="itemsData.objects.splice(keyItem, 1)"
                         v-on:item-changed="changeItem(keyItem, $event)">
      </BathStyleListItem>
    </div>
    <Pagination :route-prefix="'/bath-style/page/'" :page-count="this.paginationPageCount"/>
  </div>
</template>

<script>
import api from "../../common/api";
import BathStyleListItem from "./BathStyleListItem";
import LoaderSpinner from "../Other/LoaderSpinner";
import Pagination from "../Other/Pagination";
import {debounce} from "lodash";

export default {
  name: "BathStyleList",
  components: {
    BathStyleListItem,
    LoaderSpinner,
    Pagination
  },
  data() {
    return {
      isLoading: false,
      isLoadingData: false,
      error: null,
      itemsData: {},
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
  },
  watch: {
    pageNumber() {
      this.getItemsData();
    },
    searchString(newVal, oldVal) {
      if (newVal !== oldVal) {
        this.$router.push({name: 'bathStyleListPage', params: {'pageNumber': 1}});
      }
      this.getItemsData();
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
      api.getData('bathStyle', sendData)
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
    getItemsData: debounce(async function () {
      this.isLoadingData = true;
      this.itemsData = {};
      let pageNumber = this.pageNumber ? this.pageNumber : 1;
      this.getCount();
      let sendData = {'pagination': {'page': pageNumber, 'perPage': 20}};
      sendData = Object.assign(sendData, this.searchArray);
      api.getData('bathStyle', sendData)
          .then((r) => {
            setTimeout(() => {
              this.isLoadingData = false
            }, 1000);
            setTimeout(() => this.itemsData = r.returnData ? r.returnData : [], 1000);
            if (r.error) {
              this.error = 'Во время получения данных возникла ошибка: ' + r.error ? r.error : 'неизвестная ошибка';
            }
          })
          .catch((e) => {
            setTimeout(() => {
              this.isLoadingData = false
            }, 1500);
            this.error = e.error;
          })
    }, 1500),
    //методы добавления и реакции на изменения
    addBathStyle() {
      this.itemsData.objects.unshift({'name': ''})
    },
    changeItem(keyItem, newVal) {
      if (this.itemsData.objects[keyItem]) {
        this.itemsData.objects[keyItem] = newVal;
      }
    },
  }
}
</script>

<style scoped>

</style>