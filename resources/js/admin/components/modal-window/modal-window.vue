<template>
    <transition name="fade">
        <div class="modal-block-wrapper" @click="closedModal()">
            <div class="modal-block" @mouseenter="overModal(1)" @mouseleave="overModal(0)">
                <div class="modal-block-close" @click="$root.modalWindowToggle = false"><i class="fal fa-times"></i>
                </div>
                <div class="modal-title" v-html="valueTitle"></div>
                <div class="modal-content" v-html="valueContent"></div>
            </div>
        </div>
    </transition>
</template>

<script>
    export default {
        name: "modal-window",
        props: {
            valueTitle: '',
            valueContent: '',
        },
        data: function () {
            return {
                overModalBlock:false,
            }
        },
        computed:{

        },
        watch: {
            valueContent: function () {

            }
        },
        methods:{
            overModal:function(isOver){
                this.overModalBlock = !!isOver;
            },
            closedModal:function(){
                if(!this.overModalBlock){
                   this.$root.modalWindowToggle = false;
                }
            }
        }
    }
</script>

<style scoped>
    .fade-enter-active, .fade-leave-active {
        transition: opacity .5s;
    }

    .fade-enter, .fade-leave-to /* .fade-leave-active до версии 2.1.8 */
    {
        opacity: 0;
    }
</style>