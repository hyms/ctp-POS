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
                                    label="Secuencia"
                                    label-for="secuencia"
                                    label-cols="4"
                                    content-cols="8"
                                >
                                    <b-input
                                        type="text"
                                        placeholder="Secuencia"
                                        v-model="searchModel.secuencia"
                                        id="secuencia"
                                        name="secuencia"
                                    ></b-input>
                                </b-form-group>
                            </div>
                            <div class="col-sm-5">
                                <b-form-group
                                    label="Cliente"
                                    label-for="nombre"
                                    label-cols="4"
                                    content-cols="8"
                                >
                                    <b-input
                                        type="text"
                                        placeholder="Cliente"
                                        v-model="searchModel.nombre"
                                        id="nombre"
                                        name="nombre"
                                    ></b-input>
                                </b-form-group>
                            </div>
                            <div class="col-sm-5">
                                <b-form-group
                                    label="Detalle"
                                    label-for="detalle"
                                    label-cols="4"
                                    content-cols="8"
                                >
                                    <b-input
                                        type="text"
                                        placeholder="Detalle"
                                        v-model="searchModel.detalle"
                                        id="detalle"
                                        name="detalle"
                                    ></b-input>
                                </b-form-group>
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
                detalle: "",
                secuencia: "",
                nombre: "",
            },
            total: 0,
            cardCollapse: false,
        }
    },
    props: {
        report: Object
    },
    mounted() {
        for (const key in this.report) {
            this.searchModel[key] = this.report[key];
        }
    }
}
</script>

