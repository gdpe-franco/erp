<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';   // optional
import Row from 'primevue/row';     

const props = defineProps({
    transacciones: Array
})

const columns = [
    { field: 'id', header: 'ID' },
    { field: 'detalles', header: 'Detalles' },
    { field: 'fecha', header: 'Fecha' },
];
</script>
<template>
    <AppLayout title="Transacciones">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Transacciones
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <DataTable :value="transacciones" tableStyle="min-width: 50rem">
                        <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header"/>
                    </DataTable>
                </div>
            </div>
        </div>
    </AppLayout>
</template>