<template>
    <div class="content-w">
        <div class="content-box">
        <h1>Page</h1>
        <p>{{pass}}</p>
    </div>
    </div>
</template>
<script>
import Layout from '@/Shared/Layout'

export default {
    props: {
        pass: Object,
    },
    layout: Layout,
}

</script>
