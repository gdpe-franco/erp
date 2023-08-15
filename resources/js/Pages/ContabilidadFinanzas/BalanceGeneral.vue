<script setup>
import { computed, onMounted, ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const props = defineProps({
    balanceGeneral: Object,
    periodo: Object
})

const expandedRowGroups = ref();

const formatCurrency = (value) => {
    let absVal = Math.abs(value)
    return absVal.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
};

const calculateTotals = (ids) => {
    let total = 0;
    if (props.balanceGeneral) {
        for (let cuenta of props.balanceGeneral) {
            if (ids.includes(cuenta.tipo_cuenta_id)) {
                total += cuenta.total;
            }
        }
    }
    return formatCurrency(total);
}

const calculateTypeAccountTotal = (id) => {
    let total = 0;

    if (props.balanceGeneral) {
        for (let cuenta of props.balanceGeneral) {
            if (cuenta.tipo_cuenta_id === id) {
                total += cuenta.total;
            }
        }
    }
    
    return formatCurrency(total);
};

const deudorMovimientos = computed(() => {
    let total = 0;
    for(let balanza of Object.values(props.balanzas)) {
        total += balanza.sumaPositivos;
    }
    return formatCurrency(total);
});
</script>

<template>
    <AppLayout title="Balance General">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Balance General
            </h2>
            <h3>Periodo: {{ periodo.nombre }}</h3>
        </template>

        <div class="py-12">
            <div class="max-w-full mx-auto sm:px-6">
                <div class="bg-white overflow-hidden p-3 shadow-xl sm:rounded-lg">
                    <DataTable v-model:expandedRowGroups="expandedRowGroups" :value="balanceGeneral" tableStyle="min-width: 50rem"
                        expandableRowGroups rowGroupMode="subheader" groupRowsBy="tipo_cuenta.id"
                        sortField="tipo_cuenta.id">
                        <template #groupheader="{ data }">
                            <span class="">{{ data.tipo_cuenta.name }}</span>
                        </template>
                        <Column field="tipo_cuenta.id" header="Tipo de Cuenta"></Column>
                        <Column field="nombre" header="Cuenta" style="min-width: 200px"></Column>
                        <Column field="total" header="Total cuenta" style="min-width: 200px">
                            <template #body="{ data }">
                                {{formatCurrency(data.total)}}
                            </template>
                        </Column>
                        <template #groupfooter="{ data }">
                            <div class="flex justify-end font-bold">Total: {{ calculateTypeAccountTotal(data.tipo_cuenta_id) }}</div>
                        </template>
                    </DataTable>
                    <div class="flex card justify-around mt-6 mb-4 font-semibold">
                        <div >
                            TOTAL ACTIVOS: {{  calculateTotals([1, 2, 3]) }}
                        </div>
                        <div>
                            TOTAL PASIVOS + CAPITAL: {{  calculateTotals([4, 5, 6, 7]) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>