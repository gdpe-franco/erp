<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue'
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
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
    { field: 'id', header: 'ID', sortable: true },
    { field: 'cuenta.nombre', header: 'Cuenta', sortable: true },
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

const opcionesCuentas = {
    1: { nombre: 'Activo circulante', severity: 'info' },
    2: { nombre: 'Activo fijo', severity: 'info' },
    3: { nombre: 'Activo diferido', severity: 'info' },
    4: { nombre: 'Pasivo a corto plazo', severity: 'primary' },
    5: { nombre: 'Pasivo a largo plazo', severity: 'primary' },
    6: { nombre: 'Pasivo reservas y provisiones', severity: 'primary' },
    7: { nombre: 'Capital', severity: 'warning' },
    8: { nombre: 'Ingresos', severity: 'success' },
    9: { nombre: 'Egresos', severity: 'danger' }
}

</script>
<template>
    <AppLayout title="Detalles Transacciones">
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
                            <Button label="New" icon="bi bi-plus" severity="success" size="small" @click="openCreateDialog"/>
                        </template>
                    </Toolbar>
                    <DataTable class="p-datatable-sm" :value="detalleTransacciones" :filters="filters" tableStyle="min-width: 50rem">
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
                        <Column field="cuenta.tipo_cuenta_id" header="Tipo de cuenta" sortable>
                            <template #body="{ data }">
                                <Tag
                                    :value="opcionesCuentas[data.cuenta.tipo_cuenta_id].nombre"
                                    :severity="opcionesCuentas[data.cuenta.tipo_cuenta_id].severity">
                                </Tag>
                            </template>
                        </Column>
                        <Column field="monto" header="Monto" sortable style="min-width:8rem">
                            <template #body="{ data }">
                                {{ formatCurrency(data.monto) }}
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
        <Create v-if="createVisible" @hide="createVisible = false" :dataModal="{ 'cuentas': cuentas }"></Create>
    </AppLayout>
</template>