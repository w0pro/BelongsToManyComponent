<script setup>
import Popup from "./Popup.vue";
import { VueDraggableNext } from 'vue-draggable-next'
import {useUserApiFetch} from "../composables/useUserApiFetch";
import {ref, watch} from "vue";
const props = defineProps({
    index: Number,
    activeTab: Number,
    resourceId:Number,
    resourceName: String,
    activeModel: String
})

const searchModelData = ref([])
const activeContent = ref([])
const searchModel = async (query) => {

    const response  = await useUserApiFetch(`search-model?subjectModel=${props.activeModel}&q=${query}&objectId=${props.resourceId}&objectName=${props.resourceName}`)

    if (response.data) {
        searchModelData.value = response.data
    }
}
const setRelations = async (subjectId) => {
    const response  = await useUserApiFetch(`set-relations`, {
        objectName: props.resourceName,
        objectId: Number(props.resourceId),
        subjectModel: props.activeModel,
        subjectId: subjectId
    }, "post", )

    if (response.status === 200) {
        activeContent.value.data.push(response.data)
        searchModelData.value = []
    }
}

const getRelations = async (page = 1) => {
    const response  = await useUserApiFetch(`get-relations/${props.resourceId}?objectName=${props.resourceName}&subjectModel=${props.activeModel}&page=${page}`)

    if (response.status === 200) {
        activeContent.value = response.data
    }
}

watch(()=>props.activeTab, (n,o) => {
    if (n === props.index) {
        getRelations()
    }
})

const deleteRelation = async (id) => {
    const response  = await useUserApiFetch(`delete-relations/${props.resourceId}/?objectName=${props.resourceName}&subjectModel=${props.activeModel}&subjectId=${id}`, {}, 'delete')

    if (response.status === 200) {
        activeContent.value.data = activeContent.value.data.filter(e => e.id !== response.data.id)
    }
}


const resort = async () => {
    const sortList = document.querySelectorAll('.tab__content__item_wrapper')
    const sortData = []

    sortList.forEach((el) => {
        sortData.push({
            order_id: el.getAttribute('data-order'),
            id: el.getAttribute('data-relation'),
            user_id: 1,
            role_id: el.getAttribute('data-id')
        })
    })

    const response  = await useUserApiFetch(`sort-relations/`, {
        objectName: props.resourceName,
        subjectModel: props.activeModel,
        sortData: sortData
    }, 'post')

    if (response.status === 200) {
        activeContent.value.data = activeContent.value.data.filter(e => e.id !== response.data)
    }
}

const modelRows = ref({data: [], id: 0})

const getModelRows = async (id) => {
    const response  = await useUserApiFetch(`get-rows/${id}/?objectName=${props.resourceName}&subjectModel=${props.activeModel}`)
    if (response.status === 200) {
        modelRows.value.data = response.data
        modelRows.value.id = id
    }
}

const setChange = async (ev) => {
    const response  = await useUserApiFetch(`update-model/${ev.id}/${props.resourceName}/${props.activeModel}/`, ev.data, "post")
    if (response.status === 200) {
        modelRows.value = []
        await getRelations()
    }

}


</script>
<template>
    <Popup :data="modelRows.data" @send-change="setChange($event)" :index="modelRows.id"/>
    <div class="tab__content" v-if="index === activeTab">
        <div class="tab__search_wrapper">
            <input type="text"  @focus="searchModel($event.target.value)" class="tab__search__input" placeholder="Поиск..." @input="searchModel($event.target.value)">
            <div class="tab__search__list" :class="{active: searchModelData.length}" v-if="searchModelData.length">
                <div class="tab__search__item" @click="setRelations(model.id)" v-for="model in searchModelData" :key="model.id">
                    <span v-for="(valModel, keyModel) in model">{{keyModel}}:{{valModel}}</span>
                </div>

            </div>
        </div>
        <VueDraggableNext class="tab__content__list" :list="activeContent.data"  @change="resort" >
            <div
                ref="refElement"
                class=" tab__content__item_wrapper"
                v-for="(element, i) in activeContent.data"
                :key="element.id"
                :data-order="i+ 1"
                :data-id="element.id"
                :data-relation="element.relation_id"
                @click="getModelRows(element.id)"
            >
                <div class="tab__content__item">
                    <span :class="{d__none:keyModel ==='relation_id' || keyModel==='order_id' || keyModel === 'created_at' || keyModel === 'updated_at'}" v-for="(valModel, keyModel) in element">{{keyModel}}:{{valModel}}</span>
                </div>
                <div class="tab__content__actions">
                    <button class="delete__button" @click="deleteRelation(element.id)">
                        Del
                    </button>

                </div>
            </div>
        </VueDraggableNext>
        <div class="paginations" v-if="activeContent.last_page > 1">
            <div class="pag" :class="{active: activeContent.current_page === page}" v-for="page in activeContent.last_page" @click="getRelations(page)">
                {{page}}
            </div>

        </div>

    </div>


</template>

<style scoped>
    .paginations {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 20px;
    }

    .pag {
        border-radius: 10px;
        border: 1px solid red;
        background: white;
        cursor: pointer;
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        width: 40px;
        height: 40px;
    }

    .pag.active {
        background: red;
        color: white;
    }

</style>
