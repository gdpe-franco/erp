<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';

const props = defineProps({
    detalleTransacciones: Object,
    periodo: Object
})

const formatCurrency = (value) => {
    return value.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
};

const notEmptyTransactions = (data) => {
    if(data.length <= 0) {
        return [{monto: 0}]
    } else {
        return Object.values(data)
    }
}
</script>
<template>
    <AppLayout title="Esquemas de Mayor">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Esquemas de Mayor
            </h2>
            <h3>Periodo: {{ periodo.nombre }}</h3>
        </template>
        <div class="py-12">
            <div class="max-w-full mx-auto sm:px-6">
                <div class="bg-white overflow-hidden p-3 shadow-xl sm:rounded-lg">
                    <div class="grid grid-cols-2 gap-3">
                        <div v-for="(esquemas, index) in detalleTransacciones" class="mt-6">
                            <div class="text-center font-bold bg-purple-100 p-2">
                                {{ index }}
                            </div>
                            <div class="flex justify-center shadow-lg text-center">
                                <DataTable :value="notEmptyTransactions(esquemas.positivos)" tableStyle="min-width: 20rem">
                                    <Column field="monto" header="Ingresos" class="text-center" >
                                        <template #body="{ data }">
                                            {{ formatCurrency(data.monto) }}
                                        </template>
                                    </Column>
                                    <template #footer>
                                        Total: {{ formatCurrency(esquemas.totales.totalPositivos) }}
                                    </template>
                                </DataTable>
                                <DataTable :value="notEmptyTransactions(esquemas.negativos)" tableStyle="min-width: 20rem" >
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
                            <div class="p-2 text-center col-span-2 font-bold bg-purple-100">
                                {{ formatCurrency(esquemas.totales.total) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>