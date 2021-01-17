<template>
    <div class="content-w">
        <div class="content-box">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="header-title m-t-0 m-b-20">{{ titulo }}</h4>
                </div>
            </div>
            <div class="row">
                <div class="col">
                   <Orden :isNew="true" id="new"></Orden>
                </div>
            </div>
            {{ ordenes }}
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import Orden from '../../Shared/formularios/Orden'

export default {
    name: "Ordenes",
    layout: Layout,
    data() {
        return {
            titulo: 'Ordenes'
        }
    },
    props: {
        ordenes: Array,
        productos: Array,
    },
    components:{
        Orden
    }
}
</script>

<style scoped>

</style>
