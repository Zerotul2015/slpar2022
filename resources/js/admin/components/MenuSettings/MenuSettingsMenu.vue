<template>
  <div class="content-block box-shadow mt-2 mb-1 p-2">
    <div class="form-section form-section_column">
      <div class="input-block  input-block_highlight">
        <div>Состояние</div>
        <div class="ml-1">
          <VueToggles @click="enable = !enable"
                      :value="enable"
                      :width="'65'"
                      checkedText="вкл"
                      uncheckedText="откл"
                      checkedBg="#11994B"
          />
        </div>
      </div>
      <div class="input-block">
        <label :for="guid +'name'">Название меню:</label>
        <input :id="guid + 'name'" class="input" type="text" v-model="menu.name">
      </div>
      <div class="input-block">
        <label :for="guid + 'pos'">Расположение:</label>
        <select :id="guid +'pos'" v-model="menu.position">
          <option v-for="(positionName,positionKey) in menuPositions" :value="positionKey">
            {{ positionName }}
          </option>
        </select>
      </div>
    </div>
    <div class="form-section form-section_column">
      <div class="input-block  input-block_highlight">
        <div>Режим редактирования</div>
        <div class="ml-1">
          <VueToggles @click="editMode = !editMode"
                      :value="editMode"
                      checkedText="вкл"
                      :width="'65'"
                      uncheckedText="откл"
                      checkedBg="#11994B"
          />
        </div>
      </div>
    </div>
    <div>
      <button class="button button_small" @click="addItem">Добавит пункт меню</button>
    </div>
    <div class="form-section form-section_flex" v-if="editMode">
      <MenuSettingsMenuItem v-for="(item, keyItem) in menuItems"
                            :key="$root.guid()"
                            :menuItem="item"
                            :type-menu-item="typeMenuItem"
                            @remove-menu-item="menuItems.splice(keyItem, 1)"
      />
    </div>
    <div class="menu-preview" v-else>
      <div class="menu-item-preview background-green"
           v-for="(item, keyItem) in menu.items">
        {{ item.title }}
        <div class="menu-preview_children" v-if="item.children">
          <div class="menu-item-preview_children background-green"
               v-for="(item2, keyItem2) in item.children">
            {{ item2.title }}
            <div class="menu-preview_children" v-if="item2.children">
              <div class="menu-item-preview_children background-green"
                   v-for="(item3, keyItem3) in item2.children">
                {{ item3.title }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="buttons-block mt-2">
      <button class="button" v-html="saveButtonText" @click="saveMenu"></button>
      <button class="button  button_remove" v-html="deleteButtonText" @click="deleteMenu"></button>
    </div>
  </div>
</template>

<script>
import api from "../../common/api"
import VueToggles from "vue-toggles";
import MenuSettingsMenuItem from "./MenuSettingsMenuItem.vue";

export default {
  name: "MenuSettingsMenu",
  components: {
    VueToggles,
    MenuSettingsMenuItem
  },
  props: {
    menu: {
      type: Object,
      required: true,
    }
  },
  data() {
    return {
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
      guid: this.$root.guid(),
      editMode: false,
      menuItems: this.menu.items,
      menuPositions: {
        'header': 'В шапке сайта'
      },
      typeMenuItem: {
        'custom': 'Ссылка',
        'page': 'Страница',
        'pageCategory': 'Категория страниц',
      },
      enable: false,
    }
  },
  created() {
    this.enable = !!this.menu.enable;
  },
  watch: {
    'menu.items': function (newVal) {

    },
    menuItems: function (newVal) {
      this.menu.items =newVal;
    },
    //start save, delete
    saveStatus: function (newVal) {
      if (newVal) {
        setTimeout(() => {
          this.saveStatus = null
        }, 2500);
      }
    },
    //end save, delete
    enable(newVal) {
      this.menu.enable = newVal;
    }
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
    pages() {
      return this.$store.state.page.allById;
    },
    pageCategory() {
      return this.$store.state.pageCategory.allById;
    }
  },
  methods: {
    saveMenu(){
       api.applyData('menu', 'save', this.menu)
          .then((r) => {
            if (r.result && r.result === true) {
              this.saveStatus = 1
              if (r.returnData.id) {
                this.menu['id'] = r.returnData.id;
              }
            } else {
              this.saveStatus = 2
            }
          })
          .catch((e) => {
            this.saveStatus = 2;
            console.log(e)
          })
    },
    deleteMenu() {
      if (this.deleteStatus === null) {
        this.deleteStatus = 1;
      } else {
        if (!this.menu.id) {
          this.$emit('remove-menu');
        } else {
          api.applyData('menu', 'delete', {'id': this.menu.id} )
              .then((r) => {
                if (r.result && r.result === true) {
                  this.$emit('remove-menu');
                } else {
                  this.deleteStatus = 2
                }
              })
              .catch((e) => {
                this.deleteStatus = 2
                console.log(e)
              })
        }
      }
    },
    addItem() {
      if (!this.menuItems) {
        this.menuItems = [];
      }
      this.menuItems.push(
          {
            'title': 'Новая ссылка',
            'typeItem': 'custom',
            'value': null,
            'children': null,
          }
      );
    }
  }

}
</script>

<style scoped>
.menu-preview {
  display: flex;
  align-items: start;
  flex-wrap: wrap;
}
.menu-preview_children {
  display: flex;
  flex-direction: column;
}

.menu-item-preview {
  margin: .5rem;
  padding: .5rem;
}
.menu-item-preview_children {
  margin: 0 .25rem 0 0;
  padding: .5rem;
}
</style>