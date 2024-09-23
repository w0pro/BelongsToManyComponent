<script setup>
import { defineProps, ref } from 'vue';
import Tabs from "./Tabs.vue";
const props = defineProps({
    resourceName: String,
    resourceId:Number
})

const modelList = ref([])

import {useUserApiFetch} from "../composables/useUserApiFetch";
const getModelsList =  async () => {
    const result = await useUserApiFetch(`models?exclude=${props.resourceName}`, {})
    if (result.status === 200) {
        modelList.value = result.data
    }
}

getModelsList()

</script>

<template>
    <div>Belongs To Many Component</div>
    <div class="tabs_wrapper" v-if="modelList">

        <Tabs :list="modelList" :resourceName="resourceName" :resourceId="resourceId"/>

    </div>

</template>

<style scoped>
    .tabs_wrapper {
        margin-top: 20px;
    }



</style>

