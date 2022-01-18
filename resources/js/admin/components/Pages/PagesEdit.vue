<template>
  <div class="form">
    <h1><span v-if="pageItem.id">Редактирование</span><span v-else>Создание</span> страницы <span v-if="pageItem.title" v-html="pageItem.title"></span></h1>
    <div v-if="loading" class="loading">
      Загрузка...
    </div>
    <div v-if="error" class="error">
      {{ error }}
    </div>
    <div class="form-section">
      <div class="buttons-block">
        <button class="button button_green" v-html="saveButtonText" @click="savePageItem"></button>
        <button class="button button_remove"
                v-if="!pageItem.integrated" v-html="deleteButtonText" @click="deletePageItem">
        </button>
        <button class="button" @click="toggleSeo= !toggleSeo" v-html="buttonToggleSeo">Seo настройки</button>
        <br>
        <div v-html="errorSaveText"></div>
        <div v-html="errorDeleteText"></div>
      </div>
    </div>
    <div class="form-section" v-if="toggleSeo">
        <div class="input-block input-block_highlight">
          <label for="seo-title">Seo заголовок:</label>
          <input id="seo-title" class="input" type="text" v-model="pageItem.seo.title">
        </div>
        <div class="input-block input-block_column input-block_highlight">
          <label for="seo-description">Описание:</label>
          <textarea id="seo-description" class="textarea" v-model="pageItem.seo.description"></textarea>
        </div>
    </div>
    <div class="form-section">
      <div class="input-block input-block_highlight">
        <label for="category-page">Категория:</label>
        <select id="category-page" class="select" v-model="pageItem.category_id">
          <option :value="null">без раздела</option>
          <option v-for="(category, keyCat) in categories" :value="category.id">{{ category.name }}</option>
        </select>
      </div>
      <div class="input-block input-block_highlight">
        <label for="date-page">Дата публикации:</label>
        <input id="date-page" class="input" type="date" v-model="datePage">
      </div>
      <div class="input-block input-block_highlight">
        <label for="title-page">Название страницы:</label>
        <input id="title-page" class="input" type="text" v-model="pageItem.title">
      </div>
      <div class="input-block input-block_column input-block_highlight">
        <label for="description-page">Описание:</label>
        <textarea id="description-page" class="textarea" v-model="pageItem.description"></textarea>
      </div>
    </div>
    <div v-if="pageItem" class="input-block input-block_column input-block_highlight">
      <div>Содержимое страницы:</div>
      <editor :api-key="this.$root.TINY_API_KEY" v-model="pageItem.content"
              :init="this.$root.configEditor"></editor>
    </div>
    <div class="form-section">
      <div class="input-block">
        <input class="input input_hidden" :ref="'filesInput'" type="file"
               @change="handlerFilesUpload" multiple>
        <button @click="addImages()" class="button button_small">
          <span class="button-icon"><i class="far fa-folder-open"></i></span>
          <span class="button-text">Добавить изображения</span>
        </button>
      </div>
      <div v-for="(file, key) in imagesForUpload" class="input-block">
        {{ file.name }} ({{ Math.round(file.size / 1024) }}Kb) <span class="remove-file"
                                                                     v-on:click="removeImageUpload( key )"><i
          class="far fa-trash-alt"></i></span>
      </div>
      <div class="input-block input-block_column" v-if="this.imagesForUpload.length > 0">
        <button class="button button_small" @click="uploadImages">загрузить</button>
        <div v-html="errorUploadText" class="error-description"></div>
      </div>
      <div class="images-attaches">
        <img class="image-attaches" v-for="(imgItem, imgKey) in imagesUrl" @click="removeImagesAttaches(imgKey)"
             :src="imgItem"
             alt="изображение к новости">
      </div>
    </div>
  </div>
</template>

<script>
import api from "../../common/api";
import editor from "@tinymce/tinymce-vue"
import UploadImage from "../uploaders/upload-image";

export default {
  name: "PagesEdit",
  components: {
    UploadImage,
    editor
  },
  props: {
    id: {}
  },
  data() {
    return {
      loading: false,
      error: null,
      categories:[],
      pageItem: {},
      datePage:null,
      images: [],
      imagesForUpload: [],
      errorUploadText: null, //текст ошибки загрузки
      errorSaveText: null, //текст ошибки при  сохранения
      errorDeleteText: null, //текст ошибки удаления
      toggleSeo:false,
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
    this.getCategories();
    if (this.id) {
      this.getPageItem();
    } else {
      this.pageItem = {
        category_id:null,
        title: '',
        description: '',
        content: '',
        images:[],
        seo: {description: '', title: ''},
        url: null
      }
    }

    //добавляем изображения в переменную images
    if (this.pageItem.images && Object.keys(this.pageItem.images).length > 0) {
      let images = this.pageItem.images;
      this.pageItem.images.forEach((image, imageKey) => {
        this.images.push(image);
      });
    }
  },
  watch: {
    'pageItem.date'(newVal){
      if(newVal !== this.datePage){
        this.datePage = new Date(Date.parse(newVal)).toLocaleDateString('en-CA');
      }
    },
    datePage(newVal){
      if(newVal !== this.pageItem.date){
        this.pageItem.date = new Date(Date.parse(newVal)).toLocaleDateString('en-CA');
      }
    },
    'pageItem.images'(newVal){
      if(newVal !== this.images){
        this.images = newVal;
      }
    },
    images(newVal) {
      if(newVal !== this.pageItem.images){
        this.pageItem.images = newVal;
      }
    },
    errorUploadText(newVal) {

    },
    pageItem: {
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
    imagesUrl() {
      let returnUrl = [];
      if (this.images && this.images.length > 0) {
        this.images.forEach((imageItem, imageItemKey) => {
          if (imageItem.indexOf('/upload/temp') + 1) {
            returnUrl.push(imageItem);
          } else {
            returnUrl.push('/images/pages/' + this.pageItem.folder + '/thumb/' + imageItem);
          }
        })
      }
      return returnUrl;
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
    removeImagesAttaches(keyImage) {
      this.images.splice(keyImage, 1);
    },
    //start image upload
    removeImageUpload(keyImage) {
      this.imagesForUpload.splice(keyImage, 1);
    },
    uploadImages() {
      this.errorUploadText = null
      api.uploadFile(this.imagesForUpload)
      .then((r) => {
        if (r.result === true) {
          this.images = this.images.concat(r.returnData);
          this.imagesForUpload = [];
        } else {
          this.errorUploadText = r.error ? r.error : 'ошибка загрузки'
        }
      })
      .catch();
    },
    handlerFilesUpload() {
      let uploadedFiles = this.$refs.filesInput.files;

      for (var i = 0; i < uploadedFiles.length; i++) {
        this.imagesForUpload.push(uploadedFiles[i]);
      }
    },
    addImages() {
      this.$refs.filesInput.click();
    },
    //end image upload
    getPageItem: async function () {
      api.getData('page', {'where': 'id', 'searchString': this.id})
          .then((r) => {
            this.loading = false;
            if (r.result === true) {
              this.imagesForUpload = [];
              this.pageItem = r.returnData[0];
            }
          })
          .catch((e) => {
            this.loading = false;
            this.error = e.error ? e.error : 'неизвестная ошибка: ' + e;
          })
    },
    getCategories: async function () {
      api.getData('pageCategory', {})
          .then((r) => {
            this.loading = false;
            if (r.result === true) {
              this.categories = r.returnData;
            }
          })
          .catch((e) => {
            this.loading = false;
            this.error = e.error ? e.error : 'неизвестная ошибка: ' + e;
          })
    },
    savePageItem: async function () {
      api.applyData('page', 'save', this.pageItem)
          .then((r) => {
            if (r.result === true) {
              this.saveStatus = 1;
              this.$router.push({'name':'Pages'});
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
    deletePageItem() {
      if (this.deleteStatus === 1) {
        api.applyData('page', 'delete', {'id': this.pageItem.id})
            .then((r) => {
              if (r.result === true) {
                this.$router.push({'name':'Pages'});
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
        if (this.pageItem.id) {
          this.deleteStatus = 1;
        } else {
          this.$router.go(-1);
        }
      }
    },
  }
}
</script>

<style scoped>
.images-attaches {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  grid-gap: 1rem;
  justify-content: start;
}

.image-attaches {
  width: 100%;
  max-width: 500px
}
</style>