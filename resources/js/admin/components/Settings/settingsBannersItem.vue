<template>
  <div class="slider-item">
    <div class="input-block">
      <label :for="indexKey + 'banner-name'">Название:</label>
      <input :id="indexKey + 'banner-name'" class="input" type="text" v-model="nameBanner">
    </div>
    <br>
    <div class="input-block">
      <label :for="indexKey + 'banner-link'">Ссылка:</label>
      <input :id="indexKey + 'banner-link'" class="input" type="text" v-model="href">
    </div>
    <div class="input-block">
      <label :for="indexKey + 'banner-description'">Короткое описание:</label>
      <input :id="indexKey + 'banner-description'" class="input" type="text" v-model="description">
    </div>
    <br>
    <div class="input-block ">
      <label :for="'file-image-' + indexKey">Изображение:</label>
      <input type="file" v-on:change="newImageSet" class="input" :id="'file-image-' + indexKey" name="temp">
    </div>
    <img v-if="imageURL" class="slider-item__image" :src="imageURL">
    <div class="buttons-block mt-2">
      <button class="button button_small" v-html="saveButtonText" @click="saveBanner"></button>
      <button class="button button_small button_remove" v-html="deleteButtonText" @click="deleteBanner"></button>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import api from "../../common/api";

export default {
  name: "settingsBannersItem",
  props: {
    bannerItem: {
      type: Object,
      required: true
    },
    indexKey: {
      required: true
    }
  },
  created: function () {
  },
  data: function () {
    return {
      nameBanner: this.bannerItem.banner_name,
      image: this.bannerItem.image,
      href: this.bannerItem.href,
      description: this.bannerItem.description,
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
  watch: {
    //start save, delete
    saveStatus: function (newVal) {
      if (newVal) {
        setTimeout(() => {
          this.saveStatus = null
        }, 2500);
      }
    },
    //end save, delete
    nameBanner: function (newVal) {
      this.bannerItem.banner_name = newVal;
    },
    image: function (newVal) {
      this.bannerItem.image = newVal;
    },
    href: function (newVal) {
      this.bannerItem.href = newVal;
    },
    description: function (newVal) {
      this.bannerItem.description = newVal;
    },
  },
  computed: {
    //start save, delete
    saveButtonText: function () {
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
    deleteButtonText: function () {
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
    imageURL: function () {
      let url = '';
      if (this.image.indexOf('/upload/temp') + 1) {
        url = this.image;
      } else {
        if (this.image) {
          url = '/images/banner/' + this.image
        }
      }
      return url;
    }
  },
  methods: {
    newImageSet: async function () {
      let that = this;
      let newImage = document.querySelector('#file-image-' + this.indexKey).files[0];
      let data = new FormData();
      data.append('temp', newImage);
      await axios.post('/admin/uploader', data, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
          .then(function (response) {
            if (response.status === 200) {
              document.querySelector('#file-image-' + that.indexKey).value = '';
              that.image = response.data.url;
            }
          })
          .catch(error => {
            console.log(error.response)
          })
    },
    saveBanner: async function () {
      await api.applyData('banners', 'save', this.bannerItem)
          .then((r) => {
            if (r.result && r.result === true) {
              this.saveStatus = 1;
              this.image = this.image.split('\\').pop().split('/').pop();
            } else {
              this.saveStatus = 2;
            }
          })
          .catch((e) => {
            this.saveStatus = 2;
            console.log('Ошибка', e);
          });
    },
    deleteBanner: async function () {
      if (this.deleteStatus === 1) {
        if (this.bannerItem.id) {
          api.applyData('banners', 'delete', {'id': this.bannerItem.id})
              .then((r) => {
                if (r.result && r.result === true) {
                  this.$emit('remove-banner', this.key);
                } else {
                  this.deleteStatus = 2;
                }
              })
              .catch((e) => {
                this.deleteStatus = 2;
                console.log('Ошибка запроса:', e);
              });
        } else {
          this.$emit('remove-banner', this.indexKey);
        }
      } else {
        this.deleteStatus = 1;
      }
    }
  }
}
</script>

<style scoped>

</style>