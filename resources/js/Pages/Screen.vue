<script setup>
import { onMounted, ref } from "vue";
import Layout from "@/Layouts/Guest.vue";

const props = defineProps({
    error: Object,
});

const items = ref(
    Array.from({ length: 15 }, (k, v) => ({
        title: "Item " + v + 1,
        text: "Lorem ipsum dolor sit amet consectetur adipisicing elit. Commodi, ratione debitis quis est labore voluptatibus! Eaque cupiditate minima, at placeat totam, magni doloremque veniam neque porro libero rerum unde voluptatem!",
    }))
);

onMounted(() => {});
</script>
<template>
    <Layout>
        <div class="auth-wrapper">
            <v-row>
                <v-col cols="12">
                    <v-data-iterator :items="items">
                        <template v-slot:default="{ items }">
                            <v-row>
                                <template v-for="(item, i) in items" :key="i">
                                    <v-col cols="12" lg="2" md="3">
                                        <v-card color="secondary">
                                            <v-card-text>
                                                <div class="text-h1">
                                                    {{ item.raw.title }}
                                                </div>
                                            </v-card-text>
                                        </v-card>
                                    </v-col>
                                </template>
                            </v-row>
                        </template>
                    </v-data-iterator>
                </v-col>
            </v-row>
        </div>
    </Layout>
</template>

<style lang="scss">
.auth-wrapper {
    display: flex;
    min-height: calc(var(--vh, 1vh) * 100);
    width: 100%;
    flex-basis: 100%;
    align-items: center;
}
</style>
