<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Toolbar from 'primevue/toolbar';

import { Link } from '@inertiajs/vue3'

import Create from './Create.vue';

const props = defineProps({
    periodos: Object
})

const columns = [
    { field: 'id', header: 'ID', sortable: true },
    { field: 'nombre', header: 'Nombre', sortable: true },
    { field: 'fecha_inicio', header: 'Fecha inicio', sortable: true },
    { field: 'fecha_fin', header: 'Fecha fin', sortable: true }
];

const createVisible = ref(false)

const openCreateDialog = () => {
    createVisible.value = true;
}
</script>

<template>
    <AppLayout title="Contabilidad del periodo">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Periodos
            </h2>
        </template>       
        <div class="py-12">
            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <Toolbar class="mb-4">
                        <template #start>
                            <Button label="Crear nuevo periodo" icon="bi bi-plus" severity="success" size="small" @click="openCreateDialog"/>
                        </template>
                    </Toolbar>
                    <DataTable :value="periodos" class="p-datatable-sm" tableStyle="min-width: 50rem">
                        <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header" :sortable="col.sortable"/>
                        <Column header="Operaciones" headerStyle="text-align: center" bodyStyle="text-align: center; overflow: visible">
                            <template #body="{ data }">
                                <div class="flex justify-center gap-1">
                                    <Link :href="route('ContabilidadFinanzas.EsquemasMayor.index', data.id)">
                                        <Button type="button" icon="bi bi-table" size="small" 
                                            severity="help" v-tooltip.top="'Esquemas de Mayor'" rounded />
                                    </Link>
                                    <Link :href="route('ContabilidadFinanzas.BalanzaComprobacion.index', data.id)">
                                        <Button type="button" icon="bi bi-file-earmark-ruled" size="small" 
                                            severity="help" v-tooltip.top="'Balanza de comprobación'" rounded />
                                    </Link>
                                    <Link :href="route('ContabilidadFinanzas.EstadoResultados.index', data.id)">
                                        <Button type="button" icon="bi bi-file-diff-fill" size="small" 
                                            severity="help" v-tooltip.top="'Estado de resultados'" rounded />
                                    </Link>
                                    <Link :href="route('ContabilidadFinanzas.BalanceGeneral.index', data.id)">
                                        <Button type="button" icon="bi bi-file-earmark-spreadsheet" size="small" 
                                            severity="help" v-tooltip.top="'Balance general'" rounded />
                                    </Link>
                                </div>
                            </template>
                        </Column>
                        <Column headerStyle="text-align: center" bodyStyle="text-align: center; overflow: visible">
                            <template #body>
                                <div class="flex justify-center gap-1">
                                    <Button type="button" icon="bi bi-pencil-fill" size="small" 
                                        severity="warning" v-tooltip.top="'Editar'" rounded />
                                    <Button type="button" icon="bi bi-trash3-fill" size="small" 
                                        severity="danger" v-tooltip.top="'Eliminar'" rounded />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
        <Create v-if="createVisible" @hide="createVisible = false" :dataModal="{}"></Create>
    </AppLayout>
</template>