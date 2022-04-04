<template>
  <div style="display: grid;grid-gap:1rem; align-content: center; justify-content: left; padding:1rem;">
    <div><img class="image_thumb" :src="imageUrl"></div>
    <div>{{ item.name }}</div>
    <router-link class="button" :to="'/bath-style/edit/' + item.id" title="редактирование">
      <span class="button-icon"><i class="far fa-edit"></i></span>
    </router-link>
    <button class="button button_remove" v-html="deleteButtonText" @click="removeItem"></button>

  </div>
</template>

<script>
import api from "../../common/api";

export default {
  name: "BathStyleListItem",
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
      image:'',
      //start save, delete
      deleteStatus: null, // 1 -  требуется подтверждение, 2 - ошибка, 0 | null - без изменений
      deleteButtonDefault: '<span class="button-icon"><i class="far fa-trash-alt"></i></span',
      deleteButtonConfirm: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-text">подтвердить удаление</span>',
      deleteButtonError: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-text">ошибка при удалении</span>',
      //end save, delete
    }
  },
  created() {
    this.image = this.item.image ? this.item.image : '';
  },
  watch: {
    //start save, delete
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
          returnUrl = '/images/bath-style/' + this.item.folder + '/thumb/' + this.image;
        }
      } else {
        returnUrl = '/build/images/noimg.png'
      }
      return returnUrl;
    },
    //start save, delete
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
    removeItem() {
      this.errorText = '';
      if (this.deleteStatus === 1) {
        api.applyData('bathStyle', 'delete', {'id': this.item.id})
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
    }
  }
}
</script>

<style scoped>

</style>