<template>
    <b-button :variant="(variant)?variant:'primary'" :disabled="loading">
        <span v-if="loading">
            <b-spinner small></b-spinner>
            {{ textLoad }}...
          </span>
        <span v-else>
            {{ text }}
          </span>
    </b-button>
</template>

<script>
export default {
    props: {
        loading: Boolean,
        text: String,
        textLoad: String,
        variant: String
    },
}
</script>
