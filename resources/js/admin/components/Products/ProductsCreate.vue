<template>
  <div class="wrapper-content">
    <h1 v-html="titleText" :class="{'error':notFoundProduct}"></h1>
    <div class="wrapper-content" v-if="!notFoundProduct && Object.keys(item).length > 0">
      <div class="buttons-block">
        <button class="button button_green" v-html="saveButtonText" @click="saveItem"></button>
        <button class="button button_remove" v-html="deleteButtonText" @click="removeItem"></button>
        <button class="button" @click="toggleSeo= !toggleSeo" v-html="buttonToggleSeo">Seo настройки</button>
        <div class="error" v-html="errorText"></div>
      </div>
      <div v-if="isLoading" class="loading">
        Загрузка...
      </div>
      <div v-if="error" class="error">
        {{ error }}
      </div>
      <h2 v-if="toggleSeo">Seo настройки</h2>
      <div class="form-section" v-if="toggleSeo">
        <div class="input-block input-block_highlight">
          <label for="seo-title">Seo заголовок:</label>
          <input id="seo-title" class="input" type="text" v-model="seoDescription">
        </div>
        <div class="input-block input-block_column input-block_highlight">
          <label for="seo-description">Описание:</label>
          <textarea id="seo-description" class="textarea" v-model="seoDescription"></textarea>
        </div>
      </div>
      <h2>Основные данные</h2>
      <div class="form-section form-section_column">
        <div class="input-block input-block_column input-block_highlight">
          <label :for="'name-' + guid">Наименование:</label>
          <input :id="'name-' + guid" class="input" type="text" v-model="item.name">
        </div>
      </div>
      <div class="form-section form-section_column">
        <div class="input-block input-block_column input-block_highlight">
          <label :for="'article-' + guid">Артикул:</label>
          <input :id="'article-' + guid" class="input" type="text" v-model="item.article">
        </div>
        <div class="input-block input-block_column input-block_highlight">
          <label :for="'unit-' + guid">Ед. измерения:</label>
          <select class="select" :id="'-' + guid" v-model="item.unit_id">
            <option :value="null">По умолчанию шт.</option>
            <option v-for="(unitItem, keyUnit) in units" :value="unitItem.id">{{ unitItem.name }}</option>
          </select>
        </div>
      </div>
      <div class="form-section form-section_column">
        <div class="input-block input-block_column input-block_highlight">
          <label :for="'cat-' + guid">Категория:</label>
          <select class="select" :id="'cat-' + guid" v-model="item.category_id">
            <option :value="null">Без категории</option>
            <option v-for="(cat, keyCak) in categories" :value="cat.id">
              <span v-if="cat.parent_id && !!categories[cat.parent_id]">{{categories[cat.parent_id].name}} -> </span>{{ cat.name }}
            </option>
          </select>
        </div>
        <div class="input-block input-block_column input-block_highlight">
          <label :for="'man-' + guid">Производитель:</label>
          <select class="select" :id="'man-' + guid" v-model="item.manufacturer_id">
            <option :value="null">Без производителя</option>
            <option v-for="(man, keyCak) in manufacturers" :value="man.id">{{ man.name }}</option>
          </select>
        </div>
        <div class="input-block input-block_column input-block_highlight">
          <label :for="'stock-' + guid">Статус наличия:</label>
          <select class="select" :id="'stock-' + guid" v-model="item.stock_status">
            <option :value="null">Без статуса</option>
            <option v-for="(stStatus, keyCak) in stockStatuses" :value="stStatus.id">{{ stStatus.name }}</option>
          </select>
        </div>
      </div>
      <h2>Габариты</h2>
      <div class="form-section form-section_flex">
        <div class="input-block input-block_column input-block_highlight">
          <label :for="'dimension-length'+guid" class="label">Длина, м.:</label>
          <input :id="'dimension-length' +guid" class="input" type="text" v-model="dimensionsLength">
        </div>
        <div class="input-block input-block_column input-block_highlight">
          <label :for="'dimension-width'+guid" class="label">Ширина, м.:</label>
          <input :id="'dimension-width' +guid" class="input" type="text" v-model="dimensionsWidth">
        </div>
        <div class="input-block input-block_column input-block_highlight">
          <label :for="'dimension-height'+guid" class="label">Высота, м.:</label>
          <input :id="'dimension-height' +guid" class="input" type="text" v-model="dimensionsHeight">
        </div>
        <div class="input-block input-block_column input-block_highlight">
          <label :for="'dimension-weight'+guid" class="label">Вес, кг.:</label>
          <input :id="'dimension-weight' +guid" class="input" type="text" v-model="dimensionsWeight">
        </div>
      </div>
      <h2>Описание товара</h2>
      <editor :api-key="this.$root.TINY_API_KEY" v-model="item.description"
              :init="this.$root.configEditor"></editor>
      <h2>Изображения</h2>
      <div class="form-section">
        <div class="input-block">
          <input class="input input_hidden" :ref="'imagesInput'" type="file"
                 @change="handlerAddImagesForm" multiple>
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
        <div class="images-grid">
          <div v-for="(imgItem, imgKey) in preparedImages">
            <div class="buttons-block buttons-action-image">
              <div class="product-main-image" v-if="(item.image_main && imgItem.indexOf(item.image_main) + 1)"
                   title="Главное изображение товара">
                <i class="fas fa-crown"></i>
              </div>
              <div class="product-main-image-toggle cursor-pointer" v-else @click="setImageMain(imgKey)"
                   title="Сделать главным">
                <i class="far fa-crown"></i>
              </div>
              <div class="cursor-pointer product-image-remove" @click="removeImage(imgKey)">
                <i class="far fa-trash-alt"></i>
              </div>
            </div>
            <img class="image-thumb" :src="imgItem" onerror="this.src='/build/images/noimg.png'"
                 alt="изображение товара">
          </div>
        </div>
      </div>
      <h2>Цена</h2>
      <div class="form-section">
        <div class="input-block input-block_highlight">
          <div class="label">Цена по запросу</div>
          <VueToggles @click="priceOnRequest= !priceOnRequest"
                      :value="priceOnRequest"
                      checkedText="да"
                      uncheckedText="нет"
                      checkedBg="#11994B"
          />
        </div>
        <div class="input-block input-block_highlight">
          <label for="price">Цена:</label>
          <input id="price" class="input" type="number" v-model="price" :disabled="priceOnRequest">
        </div>
        <div class="input-block input-block_highlight">
          <label for="price">Цена без скидки:</label>
          <input id="price" class="input" type="number" v-model="priceOld" :disabled="priceOnRequest">
        </div>
      </div>
      <h2>Прочее</h2>
      <div class="form-section">
        <div class="input-block input-block_highlight">
          <div class="label">Показывать товар:</div>
          <VueToggles @click="productEnable= !productEnable"
                      :value="productEnable"
                      checkedText="да"
                      uncheckedText="нет"
                      checkedBg="#11994B"
          />
        </div>
        <div class="input-block input-block_highlight">
          <label for="priority">Приоритет<span class="ml-1"
                                               title="Чем больше приоритет, тем выше товар выводится списке"><i
              class="far fa-info-circle"></i></span>:</label>
          <input id="priority" class="input" type="number" v-model="priority">
        </div>
      </div>
      <div class="buttons-block">
        <button class="button button_green" v-html="saveButtonText" @click="saveItem"></button>
        <button class="button button_remove" v-html="deleteButtonText" @click="removeItem"></button>
        <div class="error" v-html="errorText"></div>
      </div>
    </div>
  </div>
</template>

<script>
import api from "../../common/api";
import editor from "@tinymce/tinymce-vue"
import VueToggles from "vue-toggles";

export default {
  name: "ProductsCreate",
  components: {
    editor,
    VueToggles
  },
  props: {
    id: '',
    idCopy: '',
  },
  data() {
    return {
      guid: this.$root.guid(),
      isLoading: false,
      error: null,
      errorText: null,
      notFoundProduct: false,
      toggleSeo: false,
      item: {},
      price: null,
      priceOld: null,
      priceOnRequest: false,
      productEnable: true,
      priority: 0,
      seoTitle:'',
      seoDescription:'',
      dimensionsLength: null,//длина в метрах
      dimensionsWidth: null,//ширина в метрах
      dimensionsHeight: null,//высота в метрах
      dimensionsWeight: null,//вес в килограммах
      // загрузка изображений
      imagesForUpload: [],
      errorUploadText: '',
      //start save, delete
      saveStatus: null, // 1 -  успешно, 2 - ошибка, 0 | null - без изменений
      saveButtonDefault: '<span class="button-icon"><i class="far fa-save"></i></span><span class="button-text">сохарнить</span>',
      saveButtonSuccess: '<span class="button-icon"><i class="far fa-check"></i></span><span class="button-text">изменения записаны</span>',
      saveButtonError: '<span class="button-icon"><i class="far fa-times"></i></span><span class="button-text">ошибка при сохранении</span>',
      deleteStatus: null, // 1 -  требуется подтверждение, 2 - ошибка, 0 | null - без изменений
      deleteButtonDefault: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-text">удалить</span>',
      deleteButtonConfirm: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-text">подтвердить удаление</span>',
      deleteButtonError: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-text">ошибка при удалии</span>',
      //end save, delete
    }
  },
  created() {
    if (this.id) {
      this.getProductData(this.id);
    }
    if (this.idCopy) {
      this.getProductData(this.idCopy);
    }

    if (!this.id && !this.idCopy) {
      this.item = {
        'name': '',
        'article': '',
        'unit_id': null,
        'description': '',
        'specifications': '',
        'attachments': '',
        'category_id': null,
        'manufacturer_id': null,
        'stock_status': null,
        'price': null,
        'price_old': null,
        'price_on_request': false,
        'dimensions': {
          'd_length': null,//длина в метрах
          'd_width': null,//ширина в метрах
          'd_height': null,//высота в метрах
          'd_weight': null,//вес в килограммах
        },
        'priority': 0,
        'enable': true,
        'seo': {
          'title': '',
          'description': '',
        },
        'images': [],
        'image_main': null,
      };
    }
    this.$store.dispatch('productCategory/getAllById');//получаем данные категорий товаров
    this.$store.dispatch('productUnit/getAllById');//получаем данные категорий товаров
    this.$store.dispatch('productStockStatus/getAllById');//получаем данные категорий товаров
    this.$store.dispatch('productManufacturer/getAllById');//получаем данные категорий товаров
  },
  mounted() {
  },
  watch: {
    seoDescription(newVal, oldVal){
      this.seo.description = newVal;
    },
    seoTitle(newVal, oldVal){
      this.seo.titrle = newVal;
    },
    dimensionsLength(newVal, oldVal) {
      this.item.dimensions.d_length = newVal;
    },
    dimensionsWidth(newVal, oldVal) {
      this.item.dimensions.d_width = newVal;
    },
    dimensionsHeight(newVal, oldVal) {
      this.item.dimensions.d_height = newVal;
    },
    dimensionsWeight(newVal, oldVal) {
      this.item.dimensions.d_weight = newVal;
    },
    productEnable(newVal, oldVal) {
      this.item.enable = !!newVal;
    },
    priceOnRequest(newVal, oldVal) {
      this.item.price_on_request = !!newVal;
    },
    price(newVal, oldVal) {
      let priceVal = parseFloat(newVal);
      if (isNaN(priceVal)) {
        priceVal = 0;
      }
      this.price = priceVal;
      this.item.price = priceVal;
    },
    priceOld(newVal, oldVal) {
      let priceVal = parseFloat(newVal);
      if (isNaN(priceVal)) {
        priceVal = 0;
      }
      this.priceOld = priceVal;
      this.item.price_old = priceVal;
    },
    priority(newVal, oldVal) {
      let priorityVal = parseInt(newVal);
      if (isNaN(priorityVal)) {
        priorityVal = 0;
      }
      this.priority = priorityVal;
      this.item.priority = priorityVal;
      console.log(priorityVal)
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
    preparedImages() {
      let imageReturn = [];
      if (this.item.images && this.item.images.length > 0) {
        this.item.images.forEach((image, imageKey) => {
          imageReturn.push(this.prepareUrlForImage(image));
        });
      }
      return imageReturn;
    },
    buttonToggleSeo() {
      return !this.toggleSeo ? 'Показать seo настройки' : 'скрыть seo настройки';
    },
    titleText() {
      let title = '';
      if (this.id) {
        title = 'Редактирование товара ';
      } else {
        title = 'Создание товара ';
      }
      if (this.item.name) {
        title = title + this.item.name;
      }
      if (this.item.id) {
        title = title + '(ID ' + this.item.id + ')';
      }
      if (this.notFoundProduct) {
        title = 'Такого товара не существует.';
      }
      return title;
    },
    categories() {
      return this.$store.state.productCategory.allById;
    },
    units() {
      return this.$store.state.productUnit.allById;
    },
    stockStatuses() {
      return this.$store.state.productStockStatus.allById;
    },
    manufacturers() {
      return this.$store.state.productManufacturer.allById;
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
    setImageMain(imageKey) {
      if (this.item.images[imageKey]) {
        this.item.image_main = this.item.images[imageKey];
      }
    },
    removeImage(imageKey) {
      if (this.item.images[imageKey]) {
        this.item.images.splice(imageKey, 1);
      }
    },
    prepareUrlForImage(image) {
      let returnUrl = [];
      if (image) {
        if (image.indexOf('/upload/temp') + 1) {
          returnUrl = image;
        } else {
          returnUrl = '/images/products/' + this.item.folder + '/thumb/' + image;
        }
      } else {
        returnUrl = '/build/images/noimg.png'
      }
      return returnUrl;
    },
    // загрузка изображений
    uploadImages() {
      this.errorUploadText = null
      api.uploadFile(this.imagesForUpload)
          .then((r) => {
            if (r.result === true) {
              this.item.images = this.item.images.concat(r.returnData);
              this.imagesForUpload = [];
            } else {
              this.errorUploadText = r.error ? r.error : 'ошибка загрузки'
            }
          })
          .catch();
    },
    handlerAddImagesForm() {
      let uploadedFiles = this.$refs.imagesInput.files;
      for (var i = 0; i < uploadedFiles.length; i++) {
        this.imagesForUpload.push(uploadedFiles[i]);
      }
    },
    addImages() {
      this.$refs.imagesInput.click();
    },
    // конец загрузка изображений
    getProductData(id) {
      api.getData('product', {where: 'id', 'searchString': id})
          .then((r) => {
            this.isLoading = false;
            if (r.returnData === null) {
              this.notFoundProduct = true;
            } else {
              this.item = r.returnData && r.returnData[0] ? r.returnData[0] : {};
              if (this.idCopy) {
                this.item.id = null;
                this.item.images = [];
                this.item.image_main = null;
                this.item.videos = null;
                this.item.attachments = null;
                this.item.folder = null;
                this.url = null;
              }
              this.priceOnRequest = !!this.item.price_on_request;
              this.productEnable = !!this.item.enable;
              this.price = !this.item.price ? null : this.item.price;
              this.priceOld = !this.item.price_old ? null : this.item.price_old;
              this.priority = !this.item.priority ? 0 : this.item.priority;
              this.dimensionsLength = !this.item.dimensions.d_length ? 0 : this.item.dimensions.d_length;
              this.dimensionsWidth = !this.item.dimensions.d_width ? 0 : this.item.dimensions.d_width;
              this.dimensionsHeight = !this.item.dimensions.d_height ? 0 : this.item.dimensions.d_height;
              this.dimensionsWeight = !this.item.dimensions.d_weight ? 0 : this.item.dimensions.d_weight;
              this.seoTitle = !this.item.seo.title ? 0 : this.item.seo.title;
              this.seoDescription = !this.item.seo.description ? 0 : this.item.seo.description;
            }
            if (r.error) {
              this.error = 'Во время получения данных о товаре возникла ошибка: ' + r.error ? r.error : 'неизвестная ошибка';
            }
          })
          .catch((e) => {
            this.isLoading = false;
            this.error = e.error;
          })
    },
    removeItem() {
      this.errorText = '';
      if (this.deleteStatus === 1) {
        api.applyData('product', 'delete', {'id': this.item.id})
            .then((r) => {
              if (r.result && r.result === true) {
                this.deleteStatus = 1;
                this.$router.go(-1);
              } else {
                this.deleteStatus = 2;
                this.errorText = r.error ? r.error : 'неизвестная ошибка: ' + r;
              }
            })
            .catch((e) => {
              this.deleteStatus = 2;
              this.errorText = e ? e : 'неизвестная ошибка';
            });
      } else {
        if (this.item.id) {
          this.deleteStatus = 1;
        } else {
          this.$router.go(-1);
        }
      }
    },
    async saveItem() {
      this.errorText = '';
      await api.applyData('product', 'save', this.item)
          .then((r) => {
            console.log(r);
            if (r.result && r.result === true) {
              this.saveStatus = 1;
              this.item.id = r.returnData.id ? r.returnData.id : undefined;
              this.item.folder = r.returnData.folder ? r.returnData.folder : undefined;
              this.item.url = r.returnData.url ? r.returnData.url : undefined;
            } else {
              this.saveStatus = 2;
              this.errorText = r.error ? r.error : 'неизвестная ошибка: ' + r;
            }
          }).catch((e) => {
            this.saveStatus = 2;
            this.errorText = e.error ? e.error : 'неизвестная ошибка: ' + e;
          })
    }
  }
}
</script>

<style scoped>
.product-main-image {
  color: gold;
}

.product-main-image-toggle {
  color: gray;
}

.product-main-image-toggle:hover {
  color: gold;
}

.buttons-action-image {
  justify-content: space-between;
  padding: 0 1rem;
}

.product-image-remove {
  color: grey;
}

.product-image-remove:hover {
  color: var(--red, #f80000);
}
</style>