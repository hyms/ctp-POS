<script setup>
import {ref} from "vue";
import JsonExcel from "vue-json-excel3";

const props = defineProps({
  data: Object,
  fields: Object,
  nameFile: String,
  title: {type: String, default: null},
});
const loading = ref(false)

function startDownload() {
  loading.value = true;
}

function finishDownload() {
  loading.value = false;
}

async function fetchData() {
  await setTimeout(2000);
  return props.data
}
</script>
<template>
  <json-excel
      class="v-btn text-error v-btn--density-default v-btn--size-small v-btn--variant-outlined ma-1"
      :fetch="fetchData"
      :fields="fields"
      :worksheet="title === null ? nameFile : title"
      :name="nameFile + '.xls'"
      :before-generate="startDownload"
      :before-finish="finishDownload"
  >
    <v-icon>mdi-file-excel-box</v-icon>
    Exportar
  </json-excel>
</template>
