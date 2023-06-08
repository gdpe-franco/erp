<script setup>
import { onBeforeMount, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import DataView from 'primevue/dataview';

const props = defineProps({
    estadoResultados: Object
});

const dataEstadoResultados = ref(null);
const layout = "list";

const formatCurrency = (value) =>
{
    let absVal = Math.abs(value)
    return absVal.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
};

onBeforeMount(() => {
    dataEstadoResultados.value = Object.entries(props.estadoResultados).map(
        ([label, value]) => ({ label, value }))
});

</script>
<template>
    <AppLayout title="Esquemas de Mayor">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Estado de Resultados
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-full mx-auto sm:px-6">
                <div class="bg-white overflow-hidden p-3 shadow-xl sm:rounded-lg flex justify-center">
                    <DataView :value="dataEstadoResultados" :layout="layout"
                        class="w-1/2 shadow">
                        <template #header>
                            <div class="flex justify-center">
                                
                            </div>
                        </template>
                        <template #list="{ data }">
                            <div class="flex flex-auto">
                                <div class="w-1/2 p-2 text-center">
                                    <span :class="{ 'font-semibold' : data.label == 'Utilidad del ejercicio' }">
                                        {{ data.label }}
                                    </span>
                                </div>
                                <div class="w-1/2 p-2 text-center">
                                    <span :class="{ 'font-semibold' : data.label == 'Utilidad del ejercicio' }">
                                        {{ formatCurrency(data.value) }}
                                    </span>
                                </div>
                            </div>
                        </template>
                    </DataView>
                </div>
            </div>
        </div>
    </AppLayout>
</template>