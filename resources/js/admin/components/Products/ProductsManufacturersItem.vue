<template>
  <div class="form-section list-item_not-hover">
    <div class="input-block error" v-if="!item.id" style="flex-shrink: 0;width:100%">*Это новый производитель, он еще не
      сохранен!
    </div>
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
import UploadImage from "../uploaders/upload-image";

export default {
  name: "ProductsManufacturersItem",
  components: {UploadImage},
  props: {
    item: {
      required: true,
      type: Object
    }
  },
  data() {
    return {
      guid: this.$root.guid(),
      errorText: '',
      errorUploadText: '',
      image: '',
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
  created() {
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
    imageUrl() {
      let returnUrl = [];
      if (this.image) {
        if (this.image.indexOf('/upload/temp') + 1) {
          returnUrl = this.image;
        } else {
          returnUrl = '/images/manufacturers/' + this.item.folder + '/thumb/' + this.image;
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
      api.uploadFile(fileForUpload)
          .then((r) => {
            if (r.result && r.result === true) {
              this.image = r.returnData[0];
            } else {
              this.errorUploadText = r.error ? r.error : 'ошибка загрузки'
            }
          })
          .catch()
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
        api.applyData('productManufacturer', 'delete', {'id': this.item.id})
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
    async saveItem() {
      this.errorText = '';
      await api.applyData('productManufacturer', 'save', this.item)
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