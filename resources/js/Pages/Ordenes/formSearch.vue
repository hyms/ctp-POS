<template>
    <div class="row">
        <div class="col">
            <b-card>
                <form class="form-inline" method="get">
                    <div class="col-sm-5">
                        <b-form-group
                            label="Fecha"
                            label-for="fecha"
                            label-cols="4"
                            content-cols="8"
                        >
                            <b-input
                                type="date"
                                placeholder="fecha"
                                v-model="searchModel.fecha"
                                id="fecha"
                                name="fecha"
                            ></b-input>
                        </b-form-group>
                    </div>
                    <div class="col-sm-5">
                        <b-form-group
                            label="Orden"
                            label-for="orden"
                            label-cols="4"
                            content-cols="8"
                        >
                            <b-input
                                type="text"
                                placeholder="orden"
                                v-model="searchModel.orden"
                                id="orden"
                                name="orden"
                            ></b-input>
                        </b-form-group>
                    </div>
                    <div class="col-sm-2">
                        <b-button size="sm" type="submit">Buscar</b-button>
                    </div>
                </form>
            </b-card>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            searchModel: {
                fecha: "",
                orden: ""
            },
        }
    },
    props: {
        report: Array,
    },
    mounted() {
        for (const key in this.report) {
            this.searchModel[key] = this.report[key];
        }
    }
}
</script>

