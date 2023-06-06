<script setup>
import { onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const props = defineProps({
    detalleTransacciones: Object
})

const formatCurrency = (value) => {
    return value.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
};

</script>
<template>
    <AppLayout title="Esquemas de Mayor">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Esquema de Mayor
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-full mx-auto sm:px-6">
                <div class="bg-white overflow-hidden p-3 shadow-xl sm:rounded-lg">
                    <div class="grid grid-cols-2 gap-3">
                        <div v-for="(esquemas, index) in detalleTransacciones" class="mt-6">
                            <div class="block text-center font-semibold">
                                {{ index }}
                            </div>
                            <div class="flex justify-center shadow-lg">
                                <DataTable :value="esquemas.positivos" tableStyle="min-width: 20rem">
                                    <Column field="monto" header="Ingresos" >
                                        <template #body="{ data }">
                                            {{ formatCurrency(data.monto) }}
                                        </template>
                                    </Column>
                                    <template #footer>
                                        Total: {{ formatCurrency(esquemas.totales.totalPositivos) }}
                                    </template>
                                </DataTable>
                                <DataTable :value="esquemas.negativos" tableStyle="min-width: 20rem" >
                                    <Column field="monto" header="Egresos" >
                                        <template #body="{ data }">
                                            {{ formatCurrency(data.monto) }}
                                        </template>
                                    </Column>
                                    <template #footer>
                                        Total: {{ formatCurrency(esquemas.totales.totalNegativos) }}
                                    </template>
                                </DataTable>
                            </div>
                            <div class="block text-center">
                                {{ formatCurrency(esquemas.totales.total) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>