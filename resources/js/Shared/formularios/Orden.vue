<template>
    <div>
        <b-button v-b-modal.modal-1>{{ (isNew) ? boton1 : boton2 }}</b-button>
        <b-modal
            id="modal-1"
            :title="(isNew)?titulo1:titulo2"
            @show="reset">
            <form @submit.stop.prevent="enviar">
                <b-form-group
                    :label="form.responsable.label"
                    label-for="responsable"
                >
            <b-form-input
                name="responsable"
                v-model="form.responsable.value"
                :placeholder="form.responsable.label"
                id="responsable"
            ></b-form-input>
                </b-form-group>
            <b-form-spinbutton
                id="sb-locale"
                v-model="value"
                locale="es-bo"
                min="0"
                max="10000"
                step="1"
            ></b-form-spinbutton>
            </form>
        </b-modal>
    </div>
</template>

<script>
export default {
    name: "Orden",
    props: {
        isNew: Boolean,
    },
    data() {
        return {
            boton1: "Orden Nueva",
            boton2: "Modificar",
            titulo1: "Nueva Orden",
            titulo2: "Modificar Orden",
            form: {
                correlativo: {
                    label: 'Correlativo',
                    value: null
                },
                responsable: {
                    label: 'Cliente',
                    value: null
                },
                Observaciones: {
                    label: 'Observaciones',
                    value: null
                },
                Detalle: {}
            },
            value: 0
        }
    },
    methods: {
        reset() {
            this.value = 0;
        },
        enviar()
        {
            console.log(this.form);
        }
    },
}
</script>
