<template>
    <b-button variant="primary" :disabled="loading" >
        <b-spinner small v-if="loading"></b-spinner>
        Ingresar
    </b-button>
</template>

<script>
export default {
    props: {
        loading: Boolean,
    },
}
</script>
