<script setup>
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import Button from 'primevue/button';
import Calendar from 'primevue/calendar';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';

const props = defineProps({
    dataModal: Object
})

const form = useForm({
    nombre: null,
    fecha_inicio: null,
    fecha_fin: null
})

const emit = defineEmits(['closed'])

const createDialog = ref(true);

const submit = () => {
    form.post(route('ContabilidadFinanzas.Periodos.store'), {
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
    <Dialog v-model:visible="createDialog" :style="{ width: '750px' }" header="Nuevo periodo" :modal="true"
        class="p-fluid" @hide="closed">
        <form @submit.prevent="submit(false)">
            <div class="mt-2">
                <label for="nombre">Nombre</label>
                <InputText id="nombre" v-model="form.nombre"/>
                <small class="p-error" v-if="form.errors.nombre">{{ form.errors.nombre }}</small>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-2">
                <div class="mt-2">
                    <label for="fecha_inicio">Fecha de inicio</label>
                    <Calendar id="fecha_inicio" v-model="form.fecha_inicio" dateFormat="yy/mm/dd" showIcon />
                    <small class="p-error" v-if="form.errors.fecha_inicio">{{ form.errors.fecha_inicio }}</small>
                </div>
                <div class="mt-2">
                    <label for="fecha_fin">Fecha de fin</label>
                    <Calendar id="fecha_fin" v-model="form.fecha_fin" dateFormat="yy/mm/dd" showIcon />
                    <small class="p-error" v-if="form.errors.fecha_fin">{{ form.errors.fecha_fin }}</small>
                </div>
            </div>
        </form>
        <template #footer>
            <Button label="Cancelar" icon="bi bi-x-lg" severity="danger" text raised @click="createDialog = false" />
            <Button label="Guardar" icon="bi bi-check-lg" severity="success" text raised @click="submit" :disabled="form.processing"/>
        </template>
    </Dialog>
</template>