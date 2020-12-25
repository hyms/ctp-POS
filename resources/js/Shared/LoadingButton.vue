<template>
    <b-button variant="primary" :disabled="loading" >
        <b-spinner small v-if="loading"></b-spinner>
        <span v-if="loading">
            <b-spinner small></b-spinner>
            Ingresando...
          </span>
        <span v-else>
            Ingresar
          </span>
    </b-button>
</template>

<script>
export default {
    props: {
        loading: Boolean,
    },
}
</script>
