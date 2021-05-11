<template>
    <b-button :variant="(variant)?variant:'primary'" :size="(size)?size:''" :disabled="loading">
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
        variant: String,
        size: String
    },
}
</script>
