<script setup>
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import Button from 'primevue/button';
import Calendar from 'primevue/calendar';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import InputNumber from 'primevue/inputnumber';
import Tag from 'primevue/tag';
import Textarea from 'primevue/textarea';

const props = defineProps({
    dataModal: Object
})

const form = useForm({
    cuenta_id: null,
    cuenta: null,
    transaccion_id: null,
    monto: null,
    detalles: null,
    fecha: null
})

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

const emit = defineEmits(['closed'])

const createDialog = ref(true);

const submit = () => {
    form.post(route('ContabilidadFinanzas.DetalleTransacciones.store'), {
        onSuccess: () => {
            createDialog.value = false
        },
    });
}

const closed = () => {
    emit('closed')
}
</script>


<template>
    <Dialog v-model:visible="createDialog" :style="{ width: '750px' }" header="Nueva transacción" :modal="true"
        class="p-fluid" @hide="closed">
        <form @submit.prevent="submit(false)">
            <div class="mt-2">
                <label for="cuenta_id" class="mb-3">Cuenta</label>
                <Dropdown id="cuenta_id" v-model="form.cuenta" :options="dataModal.cuentas" optionLabel="nombre"
                    placeholder="Seleccion una cuenta" filter>
                    <template #value="{ value, placeholder }">
                        <div v-if="value" class="flex items-center">
                            <div class="mx-2">{{ value.clave }}</div>
                            <div class="mx-2">{{ value.nombre }}</div>
                            <Tag class="ms-2" 
                                :value="opcionesCuentas[value.tipo_cuenta_id].nombre"
                                :severity="opcionesCuentas[value.tipo_cuenta_id].severity" />
                        </div>
                        <span v-else>
                            {{ placeholder }}
                        </span>
                    </template>
                    <template #option="{ option }">
                        <div class="flex items-center">
                            <div class="mx-2">{{ option.clave }}</div>
                            <div class="mx-2">{{ option.nombre }}</div>
                            <Tag class="ms-2"
                                :value="opcionesCuentas[option.tipo_cuenta_id].nombre"
                                :severity="opcionesCuentas[option.tipo_cuenta_id].severity" />
                        </div>
                    </template>
                </Dropdown>
                <small class="p-error" v-if="form.errors.cuenta">{{ form.errors.cuenta }}</small>
            </div>
            <div class="mt-2">
                <label for="monto">Monto</label>
                <InputNumber id="monto" v-model="form.monto" mode="currency" currency="MXN" locale="es-MX" />
                <small class="p-error" v-if="form.errors.monto">{{ form.errors.monto }}</small>
            </div>
            <div class="mt-2">
                <label for="detalles">Detalles</label>
                <Textarea id="detalles" v-model="form.detalles" required="true" rows="3" cols="20" :autoresize="false"/>
                <small class="p-error" v-if="form.errors.detalles">{{ form.errors.detalles }}</small>
            </div>
            <div class="mt-2">
                <label for="fecha">Fecha</label>
                <Calendar id="fecha" v-model="form.fecha" dateFormat="yy/mm/dd" showIcon />
                <small class="p-error" v-if="form.errors.fecha">{{ form.errors.fecha }}</small>
            </div>
        </form>
        <template #footer>
            <Button label="Cancelar" icon="bi bi-x-lg" severity="danger" text raised @click="createDialog = false" />
            <Button label="Guardar" icon="bi bi-check-lg" severity="success" text raised @click="submit" :disabled="form.processing"/>
        </template>
    </Dialog>
</template>