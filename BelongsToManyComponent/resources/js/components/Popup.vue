<script setup>
import { ref, watch } from 'vue';
const props = defineProps({
    data: Array,
    index: Number
});

const emit = defineEmits(['update:modelValue', 'sendChange']);

const isVisible = ref(!!Object.keys(props.data).length);

watch(() => props.data, (newVal) => {
    if (newVal) {
        isVisible.value = !!Object.keys(newVal).length;
    }

});

const close = () => {
    isVisible.value = false;
    emit('update:modelValue', false);
};

const getTypeField =  (field) => {
    let result = ''
    switch (field){
        case 'int':
        case 'bigint':
            result = 'number'
            break
        case 'timestamp':
            result = 'date'
            break
        default:
            result = 'text'
    }

    return result
}

const getValueField =  (field) => {
    let result = ''
    switch (field.type){
        case 'timestamp':
            result = field.value.split(' ')[0]
            break
        default:
            result = field.value
    }

    return result
}

const updateModel = async (target) => {
    isVisible.value = false;
    emit('sendChange', {data: new FormData(target), id: props.index});
}

</script>
<template>
    <div v-if="isVisible" class="popup-overlay" @click.self="close">
        <div class="popup-content">
            <button class="close-btn" @click="close">X</button>
            <form action="" @submit="updateModel($event.target)" class="pop__up__form">
               <div v-for="(field, key) in data" class="input_wrapper">
                   <label :for="key">{{key}}</label>
                   <input class="tab__search__input" :type="getTypeField(field.type)" :id="key" :name="key" :value="getValueField(field)">
               </div>

                <button class="delete__button" type="submit">Сохранить</button>
            </form>
        </div>
    </div>
</template>



<style scoped>
.pop__up__form button {
    margin-top: 20px;
}

.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 100;
}

.popup-content {
    background: white;
    padding: 20px;
    border-radius: 5px;
    max-width: 400px;
    width: 100%;
    position: relative;
}

.close-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: none;
    border: none;
    font-size: 16px;
    cursor: pointer;
}
</style>
