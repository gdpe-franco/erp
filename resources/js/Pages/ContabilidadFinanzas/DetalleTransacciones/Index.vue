<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue'
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import InputText from 'primevue/inputtext';
import Toolbar from 'primevue/toolbar';
import { FilterMatchMode } from 'primevue/api';

import Create from './Create.vue';

const props = defineProps({
    detalleTransacciones: Array,
    cuentas: Array
})
const createVisible = ref(false)

const openCreateDialog = () => {
    createVisible.value = true;
}

const columns = [
    { field: 'id', header: 'ID', sortable: false },
    { field: 'cuenta.nombre', header: 'Cuenta', sortable: true },
    { field: 'cuenta.tipo_cuenta_id', header: 'Tipo de cuenta', sortable: true },
    { field: 'transaccion.fecha', header: 'Fecha', sortable: true }
];

const filters = ref({
    'global': {value: null, matchMode: FilterMatchMode.CONTAINS},
});

const formatCurrency = (value) => {
    if(value)
        return value.toLocaleString('es-MX', {style: 'currency', currency: 'MXN'});
    return;
};

</script>
<template>
    <AppLayout title="Transacciones">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalle Transacciones
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <Toolbar class="mb-4">
                        <template #start>
                            <Button label="New" icon="bi bi-plus" severity="success" @click="openCreateDialog"/>
                        </template>
                    </Toolbar>
                    <DataTable :value="detalleTransacciones" :filters="filters" tableStyle="min-width: 50rem">
                        <template #header>
                            <div class="flex flex-wrap items-center justify-between">
                                <h4 class="m-0">Detalles de las transacciones</h4>
                                <span class="p-input-icon-left">
                                    <i class="bi bi-search" />
                                    <InputText v-model="filters['global'].value" placeholder="Buscar..." />
                                </span>
                            </div>
                        </template>
                        <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header" :sortable="col.sortable"/>
                        <Column field="monto" header="Monto" sortable style="min-width:8rem">
                            <template #body="slotProps">
                                {{ formatCurrency(slotProps.data.monto) }}
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
        <Create v-if="createVisible" @hide="createVisible = false" :dataModal="{ 'cuentas': cuentas }"></Create>
    </AppLayout>
</template>