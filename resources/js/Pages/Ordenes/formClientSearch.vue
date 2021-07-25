<template>
    <div class="row">
        <div class="col">
            <b-card>
                <form class="form-inline" method="get">
                    <div class="col-sm-5">
                        <b-form-group
                            label="Cliente"
                            label-for="Cliente"
                            label-cols="4"
                            content-cols="8"
                        >
                            <b-input
                                type="text"
                                placeholder="Cliente"
                                v-model="searchModel.responsable"
                                id="responsable"
                                name="responsable"
                            ></b-input>
                        </b-form-group>
                    </div>
                    <div class="col-sm-2">
                        <b-button size="sm" type="submit">Buscar</b-button>
                    </div>
                    <div class="col-sm-5">
                        <h4><strong>Total:</strong> {{ searchModel.total }}</h4>
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
                responsable: "",
                total: "0"
            },
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
