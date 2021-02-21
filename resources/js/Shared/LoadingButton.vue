<template>
    <b-button variant="primary" :disabled="loading" >
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
