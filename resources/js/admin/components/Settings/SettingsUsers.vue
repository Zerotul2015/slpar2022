<template>
  <div class="wrapper-content">
    <h1>Управаление пользователями</h1>
    <div v-if="loading" class="loading">
      Загрузка...
    </div>
    <div v-if="error" class="error">
      {{ error }}
    </div>
    <div class="buttons-block">
      <button class="button" @click="addUser"><i class="far fa-plus"></i> добавить</button>
    </div>
    <div v-if="users && Object.keys(users).length >0" class="content-block">
      <SettingsUsersItem v-for="(user, userItemKey, keyTemp) in users"
                         :user="user"
                         :accessLevel="accessLevel"
                         :key="$root.guid()"
                         :index-key="$root.guid()"
                         @userDeleted="removeUser(userItemKey)">
      </SettingsUsersItem>
    </div>
  </div>
</template>

<script>
import axios from "axios"
import api from "../../common/api"
import SettingsUsersItem from "./SettingsUsersItem.vue"

export default {
  name: "SettingsUsers",
  components: {
    SettingsUsersItem
  },
  data() {
    return {
      loading: false,
      error: null,
      users: [],
      accessLevel: {
        'manager': 'Менеджер',
        'admin': 'Администратор'
      }
    }
  },
  mounted() {
    this.fetchData();
  },
  watch: {
    users: {
      deep: true,
      handler: (newVal) => {
      }
    }
  },
  methods: {
    fetchData: function () {
      api.getData('users', {})
          .then(r => {
            this.loading = false;
            if (r.result && r.result === true) {
              this.users = r.returnData;
            }
          })
          .catch((e) => {
            this.loading = false;
            this.error = e;
          })
    },
    addUser: function () {
      let newItem = {
        login: '',
        access_level: '',
        pass: '',
      };
      this.users.push(newItem);
    },
    removeUser: function (index) {
      this.$delete(this.users, index);
    }
  }
}
</script>

<style scoped>

</style>