<script setup>
import { computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';

const props = defineProps({
    balanzas: Object
})

const balanzasKeys = Object.keys(props.balanzas)
const balanzasValues = Object.values(props.balanzas)

const formatCurrency = (value) => {
    let absVal = Math.abs(value)
    return absVal.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
};

const deudorMovimientos = computed(() => {
    let total = 0;
    for(let balanza of Object.values(props.balanzas)) {
        total += balanza.sumaPositivos;
    }
    return formatCurrency(total);
});

const acreedorMovimientos = computed(() => {
    let total = 0;
    for(let balanza of Object.values(props.balanzas)) {
        total += balanza.sumaNegativos;
    }
    return formatCurrency(total);
});

const deudorSaldos = computed(() => {
    let total = 0;
    for(let balanza of Object.values(props.balanzas)) {
        total += balanza.totalPositivos;
    }
    return formatCurrency(total);
});

const acreedorSaldos = computed(() => {
    let total = 0;
    for(let balanza of Object.values(props.balanzas)) {
        total += balanza.totalNegativos;
    }
    return formatCurrency(total);
});

</script>
<template>
    <AppLayout title="Esquemas de Mayor">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Balanza de Comprobación
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-full mx-auto sm:px-6">
                <div class="bg-white overflow-hidden p-3 shadow-xl sm:rounded-lg">
                    <DataTable :value="balanzasValues" tableStyle="min-width: 50rem">
                        <ColumnGroup type="header">
                            <Row>
                                <Column header="Cuentas" :rowspan="2" class="text-center"/>
                                <Column header="Balance de movimientos" :rowspan="1" :colspan="2" class="text-center"/>
                                <Column header="Balance de saldos" :rowspan="1" :colspan="2" class="text-center"/>
                            </Row>
                            <Row>
                                <Column header="Deudor" :rowspan="1" :colspan="1" />
                                <Column header="Acreedor" :rowspan="1" :colspan="1" />
                                <Column header="Deudor" :rowspan="1" :colspan="1" />
                                <Column header="Acreedor" :rowspan="1" :colspan="1" />
                            </Row>
                        </ColumnGroup>
                        <Column field="cuenta">
                            <template #body="{ data, index}">
                                {{ balanzasKeys[index] }}
                            </template>
                        </Column>
                        <Column field="sumaPositivos">
                            <template #body="{ data }">
                                {{formatCurrency(data.sumaPositivos)}}
                            </template>
                        </Column>
                        <Column field="sumaNegativos">
                            <template #body="{ data }">
                                {{ formatCurrency(data.sumaNegativos) }}
                            </template>
                        </Column>
                        <Column field="totalPositivos">
                            <template #body="{ data }">
                                {{ formatCurrency(data.totalPositivos) }}
                            </template>
                        </Column>
                        <Column field="totalNegativos">
                            <template #body="{ data }">
                                {{ formatCurrency(data.totalNegativos) }}
                            </template>
                        </Column>
                        <ColumnGroup type="footer">
                                <Row>
                                    <Column footer="Totales:"  footerStyle="text-align:right"/>
                                    <Column :footer="deudorMovimientos" />
                                    <Column :footer="acreedorMovimientos" />
                                    <Column :footer="deudorSaldos" />
                                    <Column :footer="acreedorSaldos" />
                                </Row>
                        </ColumnGroup>
                    </DataTable>
                </div>
            </div>
        </div>
    </AppLayout>
</template>