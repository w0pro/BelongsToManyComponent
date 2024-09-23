<script setup>
import {useUserApiFetch} from "../composables/useUserApiFetch";
import {ref, watch} from "vue";

const props = defineProps({
    resourceName: String,
    activeContent: Array,
    lastIndex: Number,
    resourceId:Number,
    activeTab: Number,

})


const searchModelData = ref([])
const activeContent = ref([])
const searchModel = async (query = '') => {
    const response  = await useUserApiFetch(`search-all-model?objectName=${props.resourceName}&q=${query}&objectId=${props.resourceId}`)
    if (response.data) {
        searchModelData.value = response.data
    }
}

const deleteRelation = async (activeModel, id) => {
    const response  = await useUserApiFetch(`delete-relations/${props.resourceId}/?objectName=${props.resourceName}&subjectModel=${activeModel}&subjectId=${id}`, {}, 'delete')

    if (response.status === 200) {
        activeContent.value = activeContent.value.filter(e => e.id !== response.data.id && activeModel !== response.data.subjectModel)
    }
}

const getAllRelations = async () => {
    const response  = await useUserApiFetch(`get-all-relations/${props.resourceId}/?objectName=${props.resourceName}`)

    if (response.status === 200) {
        activeContent.value = response.data
    }
}

watch(()=>props.activeTab, (n,o) => {
    if (n === props.lastIndex) {
        getAllRelations()
    }
})

</script>
<template>
    <div class="tab__content" v-if="lastIndex === activeTab">
        <div class="tab__search_wrapper">
            <input type="text" class="tab__search__input" @focus="searchModel($event.target.value)" placeholder="Поиск..." @input="searchModel($event.target.value)">
            <div class="tab__search__list" :class="{active: searchModelData.length}" v-if="searchModelData.length">
                <div class="tab__search__item"  v-for="model in searchModelData" :key="model.id">
                    <span v-for="(valModel, keyModel) in model">{{keyModel}}:{{valModel}}</span>
                </div>

            </div>
        </div>
        <div class="tab__content__list">
            <div
                class=" tab__content__item_wrapper"
                v-for="(element, i) in activeContent"
                :key="element.id"
                :data-id="element.id"
                :data-relation="element.relation_id"
            >
                <div class="tab__content__item">
                    <span :class="{d__none:keyModel ==='relation_id' || keyModel==='order_id' || keyModel==='subjectModel'}" v-for="(valModel, keyModel) in element">{{keyModel}}:{{valModel}}</span>
                </div>
                <div class="tab__content__actions">
                    <button class="delete__button" @click="deleteRelation(element.subjectModel, element.id)">
                        Detach
                    </button>

                </div>
            </div>
        </div>

    </div>


</template>



<style scoped>

</style>
