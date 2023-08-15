<script setup>
import { onBeforeMount, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from "primevue/card";
import Timeline from 'primevue/timeline';

const props = defineProps({
});

const steps = ref([
    { step: 'Definición de políticas de pago y de descuento', icon: 'bi bi-0-circle-fill', icon2: 'bi bi-file-text-fill',  color: '#006666' },
    { step: 'Definición de calendarios de pago', icon: 'bi bi-1-circle-fill',  icon2: 'bi bi-calendar3', color: '#800080' },
    { step: 'Alta de los empleados', icon: 'bi bi-2-circle-fill', icon2: 'bi bi-people-fill',  color: '#9400D3' },
    { step: 'Identificación de los movimientos de incidencias', icon: 'bi bi-3-circle-fill', icon2: 'bi bi-exclamation-triangle-fill',  color: '#990066' },
    { step: 'Identificación de conceptos de pago y descuento para la nómina.', icon: 'bi bi-4-circle-fill', icon2: 'bi bi-cash',  color: '#0000FF' },
    { step: 'Cálculo de la nómina', icon: 'bi bi-5-circle-fill', icon2: 'bi bi-calculator-fill',  color: '#4B0082' },
    { step: 'Obtención de reportes y archivos', icon: 'bi bi-6-circle-fill', icon2: 'bi bi-archive-fill',  color: '#999900' },
    { step: 'Pago de nómina', icon: 'bi bi-7-circle-fill', icon2: 'bi bi-credit-card-fill',  color: '#006600' },
    { step: 'Contabilización', icon: 'bi bi-8-circle-fill', icon2: 'bi bi-plus-slash-minus',  color: '#994C00' },
    { step: 'Presentación de informes y declaraciones', icon: 'bi bi-9-circle-fill', icon2: 'bi bi-file-spreadsheet-fill',  color: '#990000' },
]);
</script>
<template>
    <AppLayout title="Nómina">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Nómina
            </h2>
            <h3>Pasos para hacer una nómina</h3>
        </template>

        <div class="py-12">
            <div class="max-w-full mx-auto sm:px-6">
                <div class="bg-white overflow-hidden p-3 shadow-xl sm:rounded-lg flex justify-center">
                    <Timeline :value="steps" align="alternate">
                        <template #marker="slotProps">
                            <span class="flex w-10 h-10 items-center justify-center text-white rounded-full z-1 shadow-1"
                                :style="{ backgroundColor: slotProps.item.color }">
                                <i :class="slotProps.item.icon"></i>
                            </span>
                        </template>
                        <template #content="slotProps">
                            <Card>
                                <template #content>
                                    <h3 class="text-bold">
                                        {{ slotProps.item.step }}
                                    </h3>
                                    <i :class="slotProps.item.icon2"></i>
                                </template>
                            </Card>
                        </template>
                    </Timeline>
                </div>
            </div>
        </div>
</AppLayout></template>