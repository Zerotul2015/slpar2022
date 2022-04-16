<template>
  <div class="form">
    <h1><span v-if="category.id">Редактирование</span><span v-else>Создание</span> категории <span v-if="category.title" v-html="category.title"></span></h1>
    <div v-if="loading" class="loading">
      Загрузка...
    </div>
    <div v-if="error" class="error">
      {{ error }}
    </div>
    <div class="form-section">
      <div class="buttons-block">
        <button class="button button_green" v-html="saveButtonText" @click="saveCategory"></button>
        <button class="button button_remove"
                v-if="category.id && !category.integrated" v-html="deleteButtonText" @click="deleteCategory">
        </button>
        <br>
        <div class="error-description" v-html="errorSaveText"></div>
        <div class="error-description" v-html="errorDeleteText"></div>
      </div>
    </div>
    <div class="form-section">
      <div class="input-block input-block_highlight">
        <label for="seo-title">Seo заголовок:</label>
        <input id="seo-title" class="input" type="text" v-model="category.seo.title">
      </div>
      <div class="input-block input-block_column input-block_highlight">
        <label for="seo-description">Seo описание:</label>
        <textarea id="seo-description" class="textarea" v-model="category.seo.description"></textarea>
      </div>
    </div>
    <div class="form-section">
      <div class="input-block input-block_highlight">
        <label for="name-short">Название короткое:</label>
        <input id="name-short" class="input" type="text" v-model="category.name_short">
      </div>
      <div class="input-block input-block_highlight">
        <label for="name-full">Название полное:</label>
        <input id="name-full" class="input" type="text" v-model="category.name_full">
      </div>
      <div class="input-block input-block_column input-block_highlight">
        <label for="description">Описание:</label>
        <textarea id="description" class="textarea" v-model="category.description"></textarea>
      </div>
    </div>
  </div>
</template>

<script>
import api from "../../common/api";

export default {
  name: "PagesCategoriesCreate",
  props: {
    id: {}
  },
  data() {
    return {
      loading: false,
      error: null,
      category: {},
      dateVal:null,
      errorUploadText: null, //текст ошибки загрузки
      errorSaveText: null, //текст ошибки при  сохранения
      errorDeleteText: null, //текст ошибки удаления
      //start save, delete
      saveStatus: null, // 1 -  успешно, 2 - ошибка, 0 | null - без изменений
      saveButtonDefault: '<span class="button-icon"><i class="far fa-save"></i></span><span class="button-icon">сохарнить</span>',
      saveButtonSuccess: '<span class="button-icon"><i class="far fa-check"></i></span><span class="button-icon">изменения записаны</span>',
      saveButtonError: '<span class="button-icon"><i class="far fa-times"></i></span><span class="button-icon">ошибка при сохранении</span>',
      deleteStatus: null, // 1 -  требуется подтверждение, 2 - ошибка, 0 | null - без изменений
      deleteButtonDefault: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-icon">удалить</span>',
      deleteButtonConfirm: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-icon">подтвердить удаление</span>',
      deleteButtonError: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-icon">ошибка при удалии</span>',
      //end save, delete
    }
  },
  mounted() {
    if (this.id) {
      this.getCategory();
    } else {
      this.category = {
        name_short: '',
        name_full: '',
        description: '',
        seo: {description: '', title: ''},
        url: null
      }
    }
  },
  watch: {
    dateVal(newVal) {
      if (newVal !== this.category.date) {
        this.category.date = new Date(Date.parse(newVal)).toLocaleDateString('en-CA');
      }
    },
    category: {
      deep: true,
      handler(newVal, oldVal) {

      }
    },
    //start save, delete
    saveStatus: function (newVal) {
      if (newVal) {
        setTimeout(() => {
          this.saveStatus = null
        }, 2500);
      }
    },
    deleteStatus: function (newVal) {
      if (newVal === 2) {
        setTimeout(() => {
          this.saveStatus = null
        }, 2500);
      }
    },
    //end save, delete
  },
  computed: {
    buttonToggleSeo(){
      return !this.toggleSeo  ?'Показать seo настройки' : 'скрыть seo настройки';
    },
    //start save, delete
    saveButtonText() {
      if (this.saveStatus === 1) {
        return this.saveButtonSuccess;
      }
      if (this.saveStatus === 2) {
        return this.saveButtonError;
      }
      if (this.saveStatus === 0 || this.saveStatus === null) {
        return this.saveButtonDefault;
      }
    },
    deleteButtonText() {
      if (this.deleteStatus === 1) {
        return this.deleteButtonConfirm;
      }
      if (this.deleteStatus === 2) {
        return this.deleteButtonError;
      }
      if (this.deleteStatus === 0 || this.deleteStatus === null) {
        return this.deleteButtonDefault;
      }
    },
    //end save, delete
  },
  methods: {
    //end image upload
    getCategory: async function () {
      api.getData('pageCategory', {'where': 'id', 'searchString': this.id})
          .then((r) => {
            this.loading = false;
            if (r.result && r.result === true) {
              this.category = r.returnData[0];
            }
            if(r.error){
              this.error = 'Во время получения данных категории возникла ошибка: ' + r.error ? r.error : 'неизвестная ошибка';
            }
          })
          .catch((e) => {
            this.loading = false;
            this.error = 'Во время получения данных категории возникла ошибка: ' + e ? e : 'неизвестная ошибка';
          })
    },
    saveCategory: async function () {
      api.applyData('pageCategory', 'save', this.category)
          .then((r) => {
            if (r.result === true) {
              this.saveStatus = 1;
              this.$router.push({'name':'PagesCategories'});
            } else {
              this.saveStatus = 2;
              this.errorSaveText = r.error ? r.error : 'неизвестная ошибка: ' + r;
            }
          })
          .catch((e) => {
        this.saveStatus = 2;
        this.errorSaveText = e.error ? e.error : 'неизвестная ошибка: ' + e;
      })
    },
    deleteCategory() {
      if (this.deleteStatus === 1) {
        api.applyData('pageCategory', 'delete', {'id': this.category.id})
            .then((r) => {
              if (r.result === true) {
                this.$router.push({'name':'PagesCategories'});
              } else {
                this.deleteStatus = 2;
                this.errorDeleteText = r.error ? r.error : 'неизвестная ошибка: ' + r;
              }
            })
            .catch((e) => {
          this.deleteStatus = 2;
          this.errorDeleteText = e.error ? e.error : 'неизвестная ошибка: ' + e;
        })
      } else {
        this.deleteStatus = 1;
      }
    },
  }
}
</script>

<style scoped>

</style>