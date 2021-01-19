<template>

    <div>
        <div class="tabs">
          <ul class="nav nav-pills nav-fill justify-content-end">
            <li v-for="tab in tabs" :class="{ 'is-active': tab.isActive }" class="nav-item">
                <a :href="tab.href" @click="selectTab(tab)" class="nav-link">{{ tab.name }}</a>
            </li>
          </ul>
        </div>

        <div class="tabs-details">
            <slot></slot>
        </div>
    </div>

</template>

<script>
export default {
    data() {
        return {
            tabs: []
        };
    },
    created() {   
        this.tabs = this.$children;
    },  
    methods: {
        selectTab(selectedTab) {
            this.tabs.forEach(tab => {
                tab.isActive = (tab.name == selectedTab.name);
                tab.isLoading = (tab.name == selectedTab.name);
                // console.log('Tabs '+tab.name+' '+tab.isActive+' isLoading '+tab.isLoading)
            });
        }
    }
};
</script>

<style scoped>
.tabs-details {
   padding: 10px 0; 
}
.nav-pills .is-active {
    background-color: #fa824f !important;
    color: #fff !important;
    font-weight: normal;
}
</style>