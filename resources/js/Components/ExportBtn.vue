<script setup>
import JsonExcel from "vue-json-excel3";

const props = defineProps({
    data: Object,
    fields: Object,
    nameFile: String,
    title: { type: String, default: null },
});
</script>
<template>
    <v-btn
        size="small"
        class="ma-1"
        variant="outlined"
        color="error"
        prepend-icon="mdi-file-excel-box"
    >
        <json-excel
            :data="data"
            :fields="fields"
            :worksheet="title === null ? nameFile : title"
            :name="nameFile + '.xls'"
        >
            Exportar
        </json-excel>
    </v-btn>
</template>
