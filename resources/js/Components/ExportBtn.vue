<script setup>
import {ref} from "vue";
import JsonExcel from "vue-json-excel3";

const props = defineProps({
    data: Object,
    fields: Object,
    nameFile: String,
    title: { type: String, default: null },
});
const loading=ref(false)
function startDownload(){
  loading.value=true;
}
function finishDownload(){
  loading.value=false;
}
async function fetchData(){
  await setTimeout(2000);
  return props.data
}
</script>
<template>
    <v-btn
        size="small"
        class="ma-1"
        variant="outlined"
        color="error"
        prepend-icon="mdi-file-excel-box"
        :loading="loading"
    >
        <json-excel

            :fetch="fetchData"
            :fields="fields"
            :worksheet="title === null ? nameFile : title"
            :name="nameFile + '.xls'"
            :before-generate = "startDownload"
            :before-finish   = "finishDownload"
        >
            Exportar
        </json-excel>
    </v-btn>
</template>
