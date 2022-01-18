<template>
  <div class="mb-2 p-2 box-shadow form-section" v-if="userItem">
    <div class="input-block">
      <label :for="'login-' + indexKey">Логин:</label>
      <input :id="'login-' + indexKey" class="input" type="text" v-model="userItem.login">
    </div>
    <div class="input-block">
      <label :for="'level-' + indexKey">Роль:</label>
      <select class="select" :id="'level-' + indexKey" v-model="userItem.access_level">
        <option v-for="(nameLevel, valLevel) in accessLevel" :value="valLevel" v-html="nameLevel"></option>
      </select>
    </div>
    <div class="input-block" v-if="changePass || !userItem.id">
      <label :for="'pass-' + indexKey">Пароль:</label>
      <input :id="'pass-' + indexKey" class="input" type="password" v-model="userItem.pass">
    </div>
    <div class="input-block" v-else>
      <button class="button button_small" @click="changePass = true">изменить пароль</button>
    </div>
    <div class="error-description" v-html="saveErrorText">

    </div>
    <div class="buttons-block">
      <button class="button button_small" v-html="saveButtonText" @click="save" :disabled="!fieldsChecked"></button>
      <button class="button button_small" v-html="deleteButtonText" @click="deleteUser"></button>
    </div>
  </div>
</template>

<script>
import api from "../../common/api";

export default {
  name: "SettingsUsersItem",
  props: {
    user: {
      type: Object,
      required: true
    },
    accessLevel: {
      required: true
    },
    indexKey: {
      required: true
    }
  },
  data() {
    return {
      userItem: this.user,
      saveErrorText: '',
      changePass: false,
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
    if (!this.userItem.pass) {
      this.userItem.pass = '';
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
    deleteStatus: function (newVal) {
      if (newVal === 2) {
        setTimeout(() => {
          this.saveStatus = null
        }, 2500);
      }
    },
    //end save, delete
    userItem(newVal) {
      this.user = newVal;
      this.saveErrorText = '';
    },
  },
  computed: {
    fieldsChecked: function () {
      let result = false;
      if (this.userItem.id && this.userItem.access_level && this.userItem.login) {
        result = true;
      }
      if (!this.userItem.id && this.userItem.access_level && this.userItem.login && this.userItem.pass) {
        result = true;
      }
      return result;
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
    save: async function () {
      await api.applyData('users', 'save', this.userItem)
          .then((r) => {
            if (r.result && r.result === true) {
              this.saveStatus = 1;
              if (r.id) {
                this.userItem.id = r.id;
              }
            } else {
              this.saveStatus = 2;
            }
          })
          .catch((e) => {
            this.saveStatus = 2;
            this.saveErrorText = e;
            console.log('Ошибка', e);
          });
    },
    deleteUser: async function () {
      if (this.deleteStatus === 1) {
        if (this.userItem.id) {
          await api.applyData('users', 'delete', {id: this.userItem.id})
              .then((r) => {
                if (r.result && r.result === true) {
                  this.$emit('userDeleted');
                } else {
                  this.deleteStatus = 2;
                }
              }).catch((e) => {
                console.log(e);
              });
        } else {
          this.$emit('userDeleted');
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