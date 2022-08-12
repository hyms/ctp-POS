<template>
    <v-dialog v-model="dialog" max-width="250px">
        <v-card>
            <v-card-title class="text-h5">{{ deleteText }}</v-card-title>
            <v-card-text class="text-h6">
                {{ sureText }}
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn small color="error" class="ma-1" @click="closeDelete">Cancelar</v-btn>
                <v-btn small color="primary" class="ma-1" @click="deleteItemConfirm">Borrar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: {
        dialog: Boolean,
        deleteText: {
            type: String,
            default: 'Eliminar'
        },
        sureText: {
            type: String,
            default: 'Esta seguro?'
        },
        editedIndex: Number,
        basePath: String
    },
    methods: {
        closeDelete() {
            this.$emit('close');
        },
        deleteItemConfirm() {
            this.$inertia.delete(`${this.basePath}/${this.editedIndex}`)
            this.closeDelete()
        },
    },
}
</script>

<style scoped>

</style>
