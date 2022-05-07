<template>
    <div v-if="sendStatus === 1">
        <button class="button" v-html="textButton" :disabled="!buttonActive"></button>
    </div>
    <div class="form" :class="formClass" v-else>
        <div class="form-item" :class="itemClass">
            <label for="name-input" class="label">Ваше имя:</label>
            <input id="name-input" class="input" v-model="name" type="text" placeholder="Как к вам обращаться?">
        </div>
        <div class="form-item" :class="itemClass">
            <label for="contact" class="label">Телефон или e-mail:</label>
            <input id="contact" class="input" v-model="contact" type="text" placeholder="Как с вами связаться?">
        </div>
        <div class="form-item" :class="itemClass">
            <label for="form" class="label">Сообщение:</label>
            <textarea id="form" class="textarea" v-model="message" placeholder=""></textarea>
        </div>
        <button class="button" :disabled="!buttonActive" @click="sendForm">
          <icon-svg class="btn-icon" :icon="iconButton"></icon-svg>
          <span class="btn btn-text" v-html="textButton"></span>
        </button>
    </div>
</template>

<script>
    import axios from 'axios'
    import IconSvg from "../Icon-svg/icon-svg";


    export default {
        name: "feedback-form",
      components: {IconSvg},
      props: {
             // 'column' or 'row'
        },
        data: function () {
            return {
                direction: 'row',
                name: '',
                contact: '',
                message: '',
                sendStatus: null, // 1 -  успешно, 2 - ошибка 3 - не заполнены нужные поля, 0 | null - без изменений
                sendButtonDefault: 'отправить',
                sendButtonSuccess: 'Ваше сообщение отправлено',
                sendButtonError: 'ошибка сервера',
                sendButtonErrorInput: 'заполните все поля',
            }
        },
        watch:{
            contact:function(){}
        },
        computed: {
            formClass: function () {
                return 'form_' + this.direction;
            },
            itemClass: function () {
                return 'form-item_' + this.direction;
            },
            buttonActive:function(){
                if(this.contact.length < 5 && this.message.length < 5 ){
                    this.sendStatus = 3;
                    return false
                }else{
                    this.sendStatus = 0;
                    return true;
                }
            },
            iconButton:function () {
                let that = this;

                if (this.sendStatus === 1) {
                    return 'check';
                }
                if (this.sendStatus === 2) {
                    return 'xmark';
                }
                if (this.sendStatus === 3) {
                    return 'xmark';
                }
                if (this.sendStatus === 0 || this.sendStatus === null) {
                    return 'send';
                }
            },
            textButton:function () {
                let that = this;

                if (this.sendStatus === 1) {
                    return this.sendButtonSuccess;
                }
                if (this.sendStatus === 2) {
                    return this.sendButtonError;
                }
                if (this.sendStatus === 3) {
                    return this.sendButtonErrorInput;
                }
                if (this.sendStatus === 0 || this.sendStatus === null) {
                    return this.sendButtonDefault;
                }
            }
        },

        methods: {
            sendForm: async function () {
                let that = this;
                let fromUrl = window.location.href;
                await axios.post('/api/json/feedback-send',
                    {
                        'name': that.name,
                        'contact': that.contact,
                        'message': that.message,
                        'from':fromUrl
                    }
                )
                    .then(function (r) {
                        if(r.data.result === 1){
                            that.sendStatus = 1;
                        }
                        else{
                            that.sendStatus = 2;
                        }
                    })
                    .catch(function (error) {
                        that.sendStatus = 2;
                    })
            }

        }
    }

</script>

<style scoped>

</style>