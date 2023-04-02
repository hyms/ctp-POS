<template>
    <CCard>
        <CButton
            @click="cardCollapse = !cardCollapse"
            tag="button"
            color="link"
            block
            class="text-left shadow-none card-header"
        >
            <h5 class="m-0">Filtros</h5>
        </CButton>
        <CCollapse :show="cardCollapse">
            <CCardBody class="m-1">
                <div class="row">
                    <div class="col">
                            <form class="form-inline" method="get">
                                <div class="col-sm-5">
                                    <b-form-group
                                        label="Fecha"
                                        label-for="fecha Desde"
                                        label-cols="4"
                                        content-cols="8"
                                    >
                                        <b-input
                                            type="date"
                                            placeholder="fecha"
                                            v-model="searchModel.fechaI"
                                            id="fechaI"
                                            name="fechaI"
                                        ></b-input>
                                    </b-form-group>
                                </div>
                                <div class="col-sm-5">
                                    <b-form-group
                                        label="hasta"
                                        label-for="fecha"
                                        label-cols="4"
                                        content-cols="8"
                                    >
                                        <b-input
                                            type="date"
                                            placeholder="fecha hasta"
                                            v-model="searchModel.fechaF"
                                            id="fechaF"
                                            name="fechaF"
                                        ></b-input>
                                    </b-form-group>
                                </div>
                                <div class="col-sm-5">
                                    <b-form-group
                                        label="Detalle"
                                        label-for="observaciones"
                                        label-cols="4"
                                        content-cols="8"
                                    >
                                        <b-input
                                            type="text"
                                            placeholder="Detalle"
                                            v-model="searchModel.observaciones"
                                            id="observaciones"
                                            name="observaciones"
                                        ></b-input>
                                    </b-form-group>
                                </div>
                                <div class="col-sm-5" v-if="searchModel.total">
                                    <h5><strong>Total:</strong> {{ searchModel.total }}</h5>
                                </div>
                                <div class="col-12 text-center">
                                    <b-button size="sm" type="submit" variant="primary">Buscar</b-button>
                                </div>
                            </form>
                    </div>
                </div>
            </CCardBody>
        </CCollapse>
    </CCard>

</template>

<script>
export default {
    data() {
        return {
            searchModel: {
                fechaI: "",
                fechaF: "",
                observaciones: "",
            },
            total: 0,
            cardCollapse: false,
        }
    },
    props: {
        report: Object,
    },
    mounted() {
        for (const key in this.report) {
            this.searchModel[key] = this.report[key];
        }
    }
}
</script>

