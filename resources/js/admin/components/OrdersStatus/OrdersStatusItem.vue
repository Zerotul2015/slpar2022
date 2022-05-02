<template>
  <div class="form box-shadow p-2">
    <h2>{{item.name}}</h2>
    <div class="form-section">
      <div class="buttons-block">
        <button class="button button_green" v-html="saveButtonText" @click="saveItem"></button>
        <br>
        <div v-html="errorSaveText"></div>
      </div>
    </div>
    <div class="form-section">
      <div class="input-block input-block_column">
        <label :for="'description' + guid">Описание:</label>
        <textarea :id="'description' + guid" v-model="item.description"></textarea>
      </div>
    </div>
    <div class="col-2">
      <div data-v-e89ee85c="" class="input-block input-block_column">
        <label :for="'message-user' + guid">Сообщение покупателю:</label>
        <textarea :id="'message-user' + guid" v-model="item.message_mail"></textarea>
      </div>
      <div data-v-e89ee85c="" class="input-block input-block_column">
        <label :for="'message-user' + guid">Сообщение менеджеру:</label>
        <textarea :id="'message-user' + guid" v-model="item.message_mail_admin"></textarea>
      </div>
    </div>
  </div>
</template>

<script>
import api from "../../common/api";

export default {
  name: "OrdersStatusItem",
  props:{
    item:{
      type:Object,
      required:true,
    }
  },
  data() {
    return {
      guid:this.$root.guid(),
      loading: false,
      error: null,
      errorSaveText: null, //текст ошибки при  сохранения
      //start save, delete
      saveStatus: null, // 1 -  успешно, 2 - ошибка, 0 | null - без изменений
      saveButtonDefault: '<span class="button-icon"><i class="far fa-save"></i></span><span class="button-icon">сохарнить</span>',
      saveButtonSuccess: '<span class="button-icon"><i class="far fa-check"></i></span><span class="button-icon">изменения записаны</span>',
      saveButtonError: '<span class="button-icon"><i class="far fa-times"></i></span><span class="button-icon">ошибка при сохранении</span>',
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
  },
  computed: {
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
    }
    //end save, delete
  },
  methods: {
    saveItem: async function () {
      api.applyData('ordersStatus', 'save', this.item)
          .then((r) => {
            if (r.result === true) {
              this.saveStatus = 1;
            } else {
              this.saveStatus = 2;
              this.errorSaveText = r.error ? r.error : 'неизвестная ошибка: ' + r;
            }
          })
          .catch((e) => {
            this.saveStatus = 2;
            this.errorSaveText = e.error ? e.error : 'неизвестная ошибка: ' + e;
          })
    }
  }
}
</script>

<style scoped>

</style>