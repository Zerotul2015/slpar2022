<template>
  <div class="wrapper-content">
    <h1>Настройка оповещений</h1>
    <div v-if="loading" class="loading">
      Загрузка...
    </div>
    <div v-if="error" class="error">
      {{ error }}
    </div>
    <div v-if="fetchedData && Object.keys(fetchedData).length >0" class="content-block">
      <section>
        <h2>Настройки почты</h2>
        <div class="form-section">
          <div class="input-block input-block_highlight">
            <label for="login-mail">Логин от почтового серера:</label>
            <input id="login-mail" class="input form-section__input" type="text" v-model="fetchedData.email_login">
          </div>
          <div class="input-block input-block_highlight">
            <label for="pass-mail">Пароль:</label>
            <input id="pass-mail" class="input form-section__input" type="password" v-model="fetchedData.email_pass">
          </div>
          <div class="input-block input-block_highlight">
            <label for="smtp-mail">SMTP сервер:</label>
            <input id="smtp-mail" class="input form-section__input" type="text" v-model="fetchedData.smtp">
          </div>
          <div class="input-block input-block_highlight">
            <label for="send-mail">Имя для отправки email:</label>
            <input id="send-mail" class="input form-section__input" type="text" v-model="fetchedData.email_to_send">
          </div>
          <div class="input-block input-block_highlight">
            <label for="recieve-mail">Почта для получения оповещений:</label>
            <input id="recieve-mail" class="input form-section__input" type="text"
                   v-model="fetchedData.email_to_receive">
          </div>
        </div>
        <div class="buttons-block mt-2">
          <button class="button button_small" v-html="saveButtonText" @click="save"></button>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import axios from "axios"
import api from "../../common/api"

export default {
  name: "SettingsNotifications",
  data() {
    return {
      loading: false,
      error: null,
      fetchedData: {}, // основные данные компонента, запрашиваються при монтировании
      //start save, delete
      saveStatus: null, // 1 -  успешно, 2 - ошибка, 0 | null - без изменений
      saveButtonDefault: '<span class="button-icon"><i class="far fa-save"></i></span><span class="button-icon">сохарнить</span>',
      saveButtonSuccess: '<span class="button-icon"><i class="far fa-check"></i></span><span class="button-icon">изменения записаны</span>',
      saveButtonError: '<span class="button-icon"><i class="far fa-times"></i></span><span class="button-icon">ошибка при сохранении</span>',
      //end save, delete
    }
  },
  mounted() {
    this.fetchData();
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
    fetchedData: {
      deep: true,
      handler: (newVal) => {
      }
    }
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
    },
    //end save, delete
  },
  methods: {
    fetchData: async function () {
      await api.getData('settingsNotifications', {})
          .then((r) => {
            this.loading = false;
            if (r.result && r.result === true) {
              this.fetchedData = r.returnData;
            } else {
              this.error = r.error ? r.error : 'ошибка получения данных'
            }
          })
          .catch((e) => {
        this.loading = false;
        this.error = e;
      });
    },
    save: async function () {
      api.applyData('settingsNotifications', 'save', this.fetchedData)
          .then((r) => {
            if (r.result && r.result === true) {
              this.saveStatus = 1;
            } else {
              this.saveStatus = 2;
            }
          })
          .catch((e) => {
            this.saveStatus = 2;
            this.error = e;
          });
    }
  }
}
</script>

<style scoped>

</style>