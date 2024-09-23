<script setup>
import FullSearchRelation from "./FullSearchRelation.vue";

import { defineProps, ref, watch} from 'vue';
import TabContent from "./TabContent.vue";

const props = defineProps({
    list: Array,
    resourceName: String,
    resourceId:Number
})

const activeTab = ref(null)
const activeModel = ref('')



const setTab = (modelName, index) => {
    activeTab.value = index
    activeModel.value = modelName
}


</script>
<template>
    <div class="" >
        <div  class="tabs__buttons_wrapper" >
            <div class="tabs__button" v-for="(modelName, index) in list" :class="{active: index === activeTab}" @click="setTab(modelName, index)">
                {{modelName}}
            </div>
<!--            <div class="tabs__button"  :class="{active: activeTab === list.length + 1}" @click="activeTab = list.length + 1">-->
<!--                Поиск по всем отношениям-->
<!--            </div>-->

        </div>
        <div v-for="(modelName, index) in list" class="tab__content_wrapper" :class="{active: index === activeTab}">
            <TabContent :resource-id="resourceId" :resource-name="resourceName" :active-tab="activeTab" :index="index" :active-model="activeModel"/>
        </div>

<!--        <FullSearchRelation :resource-name="resourceName" :active-tab="activeTab" :last-index="list.length + 1" :resource-id="resourceId"/>-->


    </div>

</template>


<style scoped>

</style>
