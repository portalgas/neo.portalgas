<template>

    <div v-if="referents!=null" class="card-text">
      <!-- i class="fas fa-user-friends"></i -->
      <ul class="list-inline">
          <li class="list-inline-item" v-for="(referent, index) in referents" v-bind:key="'ref-'+index">
            
              <span v-if="referent.type!='referente'">({{ referent.type }})</span>
                {{ referent.name }} 
                <a v-if="referent.email!=''" class="a-mailto" target="_blank" :href="'mailto:'+referent.email">{{ referent.email }}</a>
                <span v-if="referent.phone_satispay">
                    &nbsp;{{ referent.phone_satispay }}  
                  <img src="/img/satispay-ico.png" title="il referente ha Satispy" />
                </span>
                <span v-if="referent.phone_satispay==null">
                    &nbsp;{{ referent.phone }}  
                </span>
            
          </li>
      </ul>
    </div>

</template>

<script>
export default {
  name: "referents",
  props: ['referents']
};
</script>

<style scoped></style>