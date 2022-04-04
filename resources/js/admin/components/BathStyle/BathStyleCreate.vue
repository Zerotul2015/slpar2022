<template>
  <div class="wrapper-content">
    <h1 v-html="titleText" :class="{'error':notFound}"></h1>
    <div class="form-section list-item_not-hover">
      <div class="input-block error" v-if="!item.id" style="flex-shrink: 0;width:100%">*Это новый стиль, он еще не
        сохранен!
      </div>
      <div class="row">
        <div class=" input-block row col-2-auto col_middle-left">
          <label class="label" :for="'priority-' + guid">Приоритет:</label>
          <input :id="'priority-' + guid" class="input" type="number" v-model="item.priority">
        </div>
        <div class=" input-block row col-2-auto col_middle-left">
          <label class="label" :for="'name-' + guid">Название стиля:</label>
          <input :id="'name-' + guid" class="input" type="text" v-model="item.name">
        </div>
        <div class="col-3">
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
          <div class="form-section" v-if="!!item.seo">
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
        <div class="col">
          <h3>Описание</h3>
          <editor :api-key="this.$root.TINY_API_KEY" v-model="item.description"
                  :init="this.$root.configEditor"></editor>
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
import {isNull} from "lodash";

export default {
  name: "BathStyleCreate",
  components: {
    editor
  },
  props: {
    id: '',
  },
  data() {
    return {
      guid: this.$root.guid(),
      item: {},
      notFound: false,
      errorText: '',
      errorUploadText: '',
      image: '',
      //start save, delete
      saveStatus: null, // 1 -  успешно, 2 - ошибка, 0 | null - без изменений
      saveButtonDefault: '<span class="button-icon"><i class="far fa-save"></i></span><span class="button-icon">сохранить</span>',
      saveButtonSuccess: '<span class="button-icon"><i class="far fa-check"></i></span><span class="button-icon">изменения записаны</span>',
      saveButtonError: '<span class="button-icon"><i class="far fa-times"></i></span><span class="button-icon">ошибка при сохранении</span>',
      deleteStatus: null, // 1 -  требуется подтверждение, 2 - ошибка, 0 | null - без изменений
      deleteButtonDefault: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-icon">удалить</span>',
      deleteButtonConfirm: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-icon">подтвердить удаление</span>',
      deleteButtonError: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-icon">ошибка при удалении</span>',
      //end save, delete
    }
  },
  created() {
    if (this.id) {
      this.getBathStyle(this.id);
    }
    if (!this.id) {
      this.item = {
        'name': '',
        'seo': {
          'title': '',
          'description': ''
        },
        'description': '',
        'priority': 0,
        'image': null,
        'folder': null,
        'url': null,
      };
    }
    this.image = this.item.image ? this.item.image : '';
  },
  watch: {
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
    titleText() {
      let title = '';
      if (this.id) {
        title = 'Редактирование стиля ';
      } else {
        title = 'Создание стиля ';
      }
      if (this.item.name) {
        title = title + this.item.name;
      }
      if (this.item.id) {
        title = title + '(ID ' + this.item.id + ')';
      }
      if (this.notFound) {
        title = 'Такого стиля не существует.';
      }
      return title;
    },
    imageUrl() {
      let returnUrl = [];
      if (this.image) {
        if (this.image.indexOf('/upload/temp') + 1) {
          returnUrl = this.image;
        } else {
          returnUrl = '/images/bath-style/' + this.item.folder + '/thumb/' + this.image;
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
    getBathStyle(id) {
      api.getData('bathStyle', {where: 'id', 'searchString': id})
          .then((r) => {
            this.isLoading = false;
            if (r.returnData === null) {
              this.notFound = true;
            } else {
              this.item = r.returnData && r.returnData[0] ? r.returnData[0] : {};
              this.image = this.item.image ? this.item.image : '';
            }
            if (r.error) {
              this.error = 'Во время получения данных возникла ошибка: ' + r.error ? r.error : 'неизвестная ошибка';
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
        api.applyData('bathStyle', 'delete', {'id': this.item.id})
            .then((r) => {
              if (r.result && r.result === true) {
                this.deleteStatus = 1;
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
    saveItem() {
      this.errorText = '';
      api.applyData('bathStyle', 'save', this.item)
          .then((r) => {
            if (r.result === true) {
              this.saveStatus = 1;
              console.log(r);
              this.item.id = r.returnData.id ? r.returnData.id : undefined;
            } else {
              this.saveStatus = 2;
              this.errorText = r.error ? r.error : 'неизвестная ошибка: ' + r;
            }
          })
          .catch((e) => {
            this.saveStatus = 2;
            this.errorText = e.error ? e.error : 'неизвестная ошибка: ' + e;
          })
    }
  }
}
</script>

<style scoped>

</style>