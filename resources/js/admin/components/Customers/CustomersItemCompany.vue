<template>
  <div class="grid customer-company p-1">
    <div class="form-section form-section_column" v-if="editToggle ===false"
         style="grid-gap: 1rem;align-items: center;">
      <div v-if="!!company_.deleted" class="row" style="color: var(--red)">
        Контрагент удален!
      </div>
      <div>
        {{ company_.name }}
      </div>
      <div>
        ИНН:{{ company_.inn }}
      </div>
      <div>
        КПП:{{ company_.kpp?company_.kpp:'---' }}
      </div>
      <button class="button button_small" @click="editToggle = true">
        <span class="button-icon"><i class="far fa-edit"></i></span>
        <span class="button-text">изменить</span>
      </button>
      <button class="button button_small" title="заметки" @click="$refs[guid + 'note_hidden'].open()">
        <span class="button-icon"><i class="far fa-comment-alt-lines"></i></span>
        <span class="button-text">заметки</span>
      </button>
      <router-link class="button button_small" :to="'/orders/company/' + company_.id">
        <span class="button-icon"><i class="fab fa-shopify"></i></span>
        <span class="button-text">заказы</span>
      </router-link>
      <button v-if="!company_.deleted" class="button button_red button_small" v-html="deleteButtonText" @click="removeItem"></button>
      <button v-else class="button button_red button_small"  @click="restoreItem">
        <span class="button-icon"><i class="far fa-trash-restore"></i></span>
        <span class="button-text">восстановить</span>
      </button>
    </div>
    <div v-else>
      <div class="form-section form-section_column">
        <div class="input-block input-block_column">
          <label class="label" :for="'company-name-' + guid">Наименование</label>
          <input class="text" :id="'company-name-' + guid" type="text" v-model="company_.name">
        </div>
        <div class="input-block input-block_column">
          <label class="label" :for="'company-inn-' + guid">ИНН</label>
          <input class="text" :id="'company-inn-' + guid" type="number" minlength="10" maxlength="12"
                 v-model="company_.inn">
        </div>
        <div class="input-block input-block_column">
          <label class="label" :for="'company-inn-' + guid">КПП</label>
          <input class="text" :id="'company-kpp-' + guid" type="number" minlength="9" maxlength="9"
                 v-model="company_.kpp">
        </div>
      </div>
      <div class="buttons-block">
        <button class="button button_small button_green" v-html="saveButtonText" @click="saveItem"></button>
        <button class="button button_small" title="примечание" @click="$refs[guid + 'note_hidden'].open()">
          <span class="button-icon"><i class="far fa-comment-alt-lines"></i></span>
          <span class="button-text">Заметки</span>
        </button>
        <router-link class="button button_small" :to="'/orders/company/' + company_.id">
          <span class="button-icon"><i class="fab fa-shopify"></i></span>
          <span class="button-text">заказы</span>
        </router-link>
        <button class="button button_red button_small" v-html="deleteButtonText" @click="removeItem"></button>
        <div v-if="errorText.length > 0" class="errorText" v-html="errorText"></div>
      </div>
    </div>
    <sweet-modal :ref="guid + 'note_hidden'" :title="'Заметки по контрагенту <b>' + company_.name + '</b>'">
      <sweet-modal-tab title="Заметки покупателя" :id="'tab1' + guid">
        <textarea v-if="editToggle === true" class="width-100" :id="'note' + guid"
                  v-model="company_.note"></textarea>
        <div v-else v-html="company_.note?company_.note:'Ничего не указано.'"></div>
      </sweet-modal-tab>
      <sweet-modal-tab title="Служебные(покупатель их не увидит)" :id="'tab2' + guid">
        <textarea v-if="editToggle === true" class="width-100" :id="'note-hidden' + guid"
                  v-model="company_.note_hidden"></textarea>
        <div v-else v-html="company_.note_hidden?company_.note_hidden : 'Ничего не указано.'"></div>
      </sweet-modal-tab>
      <button v-if="editToggle === false" slot="button" class="button" @click="editToggle = true">редактировать</button>
      <button v-else class="button button_small" v-html="saveButtonText" @click="saveItem"></button>
      <div v-if="errorText.length > 0" class="errorText" v-html="errorText"></div>
    </sweet-modal>
  </div>
</template>

<script>
import api from "../../common/api";

export default {
  name: "CustomersItemCompany",
  props: {
    company: {
      type: Object
    },
    customerId: {
      required: true,
    }
  },
  data() {
    return {
      guid: this.$root.guid(),
      errorText: "",
      editToggle: false,
      company_: {
        'id': null,
        'customer_id': this.customerId,
        'name': '',
        'inn': '',
        'kpp': '',
        'note': '',
        'note_hidden': '',
      },
      //start save, delete
      saveStatus: null, // 1 -  успешно, 2 - ошибка, 0 | null - без изменений
      saveButtonDefault: '<span class="button-icon"><i class="far fa-save"></i></span><span class="button-text">сохарнить</span>',
      saveButtonSuccess: '<span class="button-icon"><i class="far fa-check"></i></span><span class="button-text">изменения записаны</span>',
      saveButtonError: '<span class="button-icon"><i class="far fa-times"></i></span><span class="button-text">ошибка при сохранении</span>',
      deleteStatus: null, // 1 -  требуется подтверждение, 2 - ошибка, 0 | null - без изменений
      deleteButtonDefault: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-text">удалить</span>',
      deleteButtonConfirm: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-text">подтвердить удаление</span>',
      deleteButtonError: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-text">ошибка</span>',
      //end save, delete
    }
  },
  created() {
    if (this.company) {
      this.company_ = {...this.company_, ...this.company};
    }
  },
  watch: {
    errorText(){},
    'company_.id'() {
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
    async saveItem() {
      this.errorText = '';
      await api.applyData('customerCompany', 'save', this.company_)
          .then((r) => {
            console.log(r);
            if (r.result && r.result === true) {
              this.editToggle = false;
              this.saveStatus = 1;
              this.company_.id = r.returnData.id ? r.returnData.id : undefined;
            } else {
              this.saveStatus = 2;
              this.errorText = r.error ? r.error : 'неизвестная ошибка: ' + r;
            }
          }).catch((e) => {
            this.saveStatus = 2;
            this.errorText = e.error ? e.error : 'неизвестная ошибка: ' + e;
          })
    },
    removeItem() {
      this.errorText = '';
      if (this.deleteStatus === 1) {
        api.applyData('customerCompany', 'delete', {'id': this.company_.id})
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
        if (this.company_.id) {
          this.deleteStatus = 1;
        } else {
          this.$emit('item-removed')
        }
      }
    },
    restoreItem(){
      this.company_.deleted = 0;
      this.saveItem();
    }
  }
}
</script>

<style scoped>

</style>