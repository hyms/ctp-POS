<template>
    <div v-if="hasErrors">
        <v-alert class="mb-4" type="error">
            <ul class="list-disc list-inside text-sm">
                <li v-for="(error, key) in errors" :key="key">{{ error }}</li>
            </ul>
        </v-alert>
    </div>
</template>
<script setup>

export default {
    props: {
        errors: Object,
    },
    data() {
        return {
            hasErrors: Object.keys(this.errors).length > 0
        }
    },
    // computed: {
    //     // errors: ()=>{
    //     //     return this.$page.props.errors;
    //     // },
    //     hasErrors: () => {
    //         console.log(Object.keys(this.errors).length);
    //         return Object.keys(this.errors).length > 0;
    //     }
    // }
}
</script>
