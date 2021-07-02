<template>
  <div>
 --------- {{ modalContent.extra }} -----------
    <transition name="fade">
      <div class="modal-wrapper" v-show="showModalSupplier" tabindex="-1" role="dialog">

          <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">{{modalContent.title}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi" @click="closeModal()">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">

                <div v-if="modalContent.extra!=null">
                  {{ modalContent.extra.name }}
                </div>

              </div>
              <div class="modal-footer">
                {{modalContent.footer}}
                <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal()">Chiudi</button>
              </div>
            </div>
          </div>

      </div>
    </transition>
    <transition name="fade">
        <mask-component v-show="showModalSupplier"/>
    </transition>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import mask from "./Mask.vue";

export default {
  name: "app-modal-supplier",
  components: {
    maskComponent: mask,
  },
  computed: {
    ...mapGetters({
      showModalSupplier: "getShowModalSupplier", 
      modalContent: "getModalContent"
    })
  },
  methods: {
    ...mapActions(['showOrHiddenModalSupplier']),
    closeModal() {
      this.showOrHiddenModalSupplier();
    },
  },
};
</script>

<style scoped>
.modal-wrapper {
  width: 100%;
  height: 300px;
  box-sizing: border-box;
  padding: 1em;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  /* background-color: #fff; */
  box-shadow: 0 0 10px rgba(144,144,144,.2);
  border: 0;
  border-radius: 5px;
  line-height: 1.5em;
  opacity: 1;
  transition: all .5s;
  z-index: 2;
  min-height: calc(100% - (1.75rem * 2));
}
.modal-body {
  overflow-y: auto;
  height: 400px;
}
@media (min-width: 576px)
.modal-dialog-centered {
    min-height: calc(100% - (1.75rem * 2));
}
.modal-dialog-centered {
    min-height: calc(100% - (1.75rem * 2));
}

@media (min-width: 1200px)
.modal-xl {
    max-width: 1140px;
}
@media (min-width: 992px)
.modal-lg, .modal-xl {
    max-width: 800px;
}

.fade-enter, .fade-leave-to {
  opacity: 0;
}
</style>
