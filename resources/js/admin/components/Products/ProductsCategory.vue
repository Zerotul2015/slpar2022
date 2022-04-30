<template>
  <div class="grid background-green p-2">
    <div class="input-block error" v-if="!item.id" style="flex-shrink: 0;width:100%">*Это новая категория, она еще не
      сохранена!
    </div>
    <div class="col">
      <h2 v-html="item.name"></h2>
    </div>
    <div class="row" v-if="toggleShow">
      <div class="col-3-auto col_middle-left">
        <label :for="'move-to-' + guid">Переместить в </label>
        <select :id="'move-to-' + guid" v-model="moveTo">
          <option :value="0">--ОСНОВНЫЕ КАТЕГОРИИ--</option>
          <option v-for="(cat, keyCak) in productsCategories" :value="cat.id">
            <span v-if="cat.parent_id && productsCategories[cat.parent_id]">{{ productsCategories[cat.parent_id].name }} -> </span>{{
              cat.name
            }}
          </option>
        </select>
        <button class="button button_small" @click="moveCategory">переместить</button>
      </div>
    </div>
    <div class="row" v-if="toggleShow">
      <div class=" input-block row col-2-auto col_middle-left">
        <label class="label" :for="'priority-' + guid">Приоритет:</label>
        <input :id="'priority-' + guid" class="input" type="number" v-model="item.priority">
      </div>
      <div class=" input-block row col-2-auto col_middle-left">
        <label class="label" :for="'priority-' + guid">Произвольная ссылке:</label>
        <VueToggles @click="isCustomLink = !isCustomLink"
                    :value="isCustomLink"
                    checkedText="вкл"
                    uncheckedText="откл"
                    checkedBg="rgb(102, 174, 50)"
        />
      </div>
      <div class="col" v-if="isCustomLink">
        <div class=" input-block">
          <label class="label" :for="'link-' + guid">Ссылка:</label>
          <input :id="'link-' + guid" class="input" type="text" v-model="item.custom_link">
        </div>
      </div>
      <div class="col-3" v-else>
        <div class="input-block input-block_column">
          <img class="image-thumb click-remove" :src="imageUrl" @click="image = null">
          <input class="input input_hidden" :ref="'filesInput'" type="file"
                 @change="handleFilesUpload">
          <button @click="addImage()" class="button button_small">
            <span class="button-icon"><i class="far fa-folder-open"></i></span>
            <span class="button-text">загрузить изображение</span>
          </button>
          <div class="error" v-html="errorUploadText"></div>
        </div>
        <div class="form-section">
          <div class="input-block input-block_column">
            <label :for="'name-' + guid">Наименование:</label>
            <input :id="'name-' + guid" class="input" type="text" v-model="item.name">
          </div>
          <div class="input-block input-block_column">
            <label :for="'description-' + guid">Описание:</label>
            <textarea :id="'description-' + guid" v-model="item.description"></textarea>
          </div>
        </div>
        <div class="form-section">
          <div class="input-block input-block_column">
            <label :for="'name-' + guid">Заголовок(SEO):</label>
            <input :id="'name-' + guid" class="input" type="text" v-model="item.seo.title">
          </div>
          <div class="input-block input-block_column">
            <label :for="'description-' + guid">Описание(SEO):</label>
            <textarea :id="'description-' + guid" v-model="item.seo.description"></textarea>
          </div>
        </div>
      </div>
      <div class="col-3-auto col_middle-left" v-if="!isCustomLink">
        <label :for="'move-to-' + guid">Содержит товары для:</label>
        <select :id="'move-to-' + guid" v-model="item.binding_style">
          <option value="bath">Бань и саун</option>
          <option value="fireplace">Каминов и печей</option>
          <option value="homestead">Каминов и печей</option>
        </select>
      </div>
    </div>
    <div class="row">
      <div class="col buttons-block">
        <button class="button button_small" @click="toggleShow = !toggleShow" v-html="toggleButtonText">подробнее
        </button>
        <router-link class="button button_small" v-if="item.id" :to="'/products/categories/' + item.id">подкатегории
        </router-link>
        <button class="button button_green button_small" v-html="saveButtonText" @click="saveItem"></button>
        <button class="button button_remove button_small" v-html="deleteButtonText" @click="removeItem"></button>
        <div class="error" v-html="errorText"></div>
      </div>
    </div>
  </div>
</template>

<script>
import api from "../../common/api";
import UploadImage from "../uploaders/upload-image";
import VueToggles from "vue-toggles";

export default {
  name: "ProductsCategory",
  components: {UploadImage, VueToggles},
  props: {
    item: {
      required: true,
      type: Object
    }
  },
  data() {
    return {
      moveTo: null,
      toggleShow: false,
      isCustomLink: false,
      guid: this.$root.guid(),
      errorText: '',
      errorUploadText: '',
      image: '',
      toggleButtonClose: '<span class="button-icon"><i class="far fa-compress-arrows-alt"></i></span><span class="button-icon">свернуть</span>',
      toggleButtonOpen: '<span class="button-icon"><i class="far fa-expand-arrows-alt"></i></span><span class="button-icon">подробнее</span>',
      //start save, delete
      saveStatus: null, // 1 -  успешно, 2 - ошибка, 0 | null - без изменений
      saveButtonDefault: '<span class="button-icon"><i class="far fa-save"></i></span><span class="button-icon">сохранить</span>',
      saveButtonSuccess: '<span class="button-icon"><i class="far fa-check"></i></span><span class="button-icon">изменения записаны</span>',
      saveButtonError: '<span class="button-icon"><i class="far fa-times"></i></span><span class="button-icon">ошибка при сохранении</span>',
      deleteStatus: null, // 1 -  требуется подтверждение, 2 - ошибка, 0 | null - без изменений
      deleteButtonDefault: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-icon">удалить</span>',
      deleteButtonConfirm: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-icon">подтвердить удаление</span>',
      deleteButtonError: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-icon">ошибка при удалии</span>',
      //end save, delete
    }
  },
  beforeCreate() {

  },
  created() {
    this.toggleShow = !this.item.id;
    this.image = this.item.image ? this.item.image : '';
    this.isCustomLink = this.item.is_custom ? this.item.is_custom : false;
    if (!this.item.binding_style) {
      this.item.binding_style = 'bath';
    }
  },
  watch: {
    isCustomLink(newVal) {
      this.item.is_custom = newVal;
    },
    image(newVal) {
      if (newVal !== this.item.image) {
        this.item.image = newVal;
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
    productsCategories() {
      return this.$store.state.productCategory.allById;
    },
    toggleButtonText() {
      return this.toggleShow ? this.toggleButtonClose : this.toggleButtonOpen;
    },
    imageUrl() {
      let returnUrl = [];
      if (this.image) {
        if (this.image.indexOf('/upload/temp') + 1) {
          returnUrl = this.image;
        } else {
          returnUrl = '/images/categories/' + this.item.folder + '/thumb/' + this.image;
        }
      } else {
        returnUrl = '/build/images/noimg.png'
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
    uploadFile(fileForUpload) {
      this.errorUploadText = null
      api.uploadFile(fileForUpload).then((r) => {
        if (r.result && r.result === true) {
          this.image = r.returnData[0];
        } else {
          this.errorUploadText = r.error ? r.error : 'ошибка загрузки'
        }
      }).catch((e) => {
      })
    },
    handleFilesUpload() {
      let filesForUpload = this.$refs.filesInput.files;
      let arrayFiles = [];
      for (var i = 0; i < filesForUpload.length; i++) {
        arrayFiles.push(filesForUpload[i]);
      }
      this.uploadFile(arrayFiles);
    },
    addImage() {
      this.$refs.filesInput.click();
    },
    removeItem() {
      this.errorText = '';
      if (this.deleteStatus === 1) {
        api.applyData('productCategory', 'delete', {'id': this.item.id})
            .then((r) => {
              if (r.result && r.result === true) {
                this.deleteStatus = 1;
                this.$emit('item-removed')
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
          this.$emit('item-removed')
        }
      }
    },
    moveCategory() {
      if (this.moveTo !== undefined) {
        this.item.parent_id = this.moveTo;
        this.saveItem();
      }
    },
    async saveItem() {
      this.errorText = '';
      await api.applyData('productCategory', 'save', this.item)
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

</style>