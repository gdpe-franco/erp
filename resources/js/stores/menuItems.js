import { defineStore } from "pinia";
import { router } from "@inertiajs/vue3";

export const useMenuItemsStore = defineStore("menuItem", {
    state: () => ({
        items: [
            {
                label: 'Contabilidad y Finanzas',
                icon: 'bi bi-bank',
                items: [
                    {
                        label: 'Detalles Transacciones',
                        icon: 'bi-currency-dollar',
                        command: () => {
                            router.visit(route('ContabilidadFinanzas.DetalleTransacciones.index'))
                         }
                    },
                    {
                        label: 'Transacciones',
                        icon: 'bi-arrow-left-right',
                        command: () => {
                           router.visit(route('ContabilidadFinanzas.Transacciones.index'))
                        }
                    },
                    {
                        label: 'Cuentas',
                        icon: 'bi-credit-card',
                    },
                    {
                        separator: true
                    },
                    {
                        label: 'Periodos',
                        icon: 'bi bi-calendar2-range',
                        command: () => {
                            router.visit(route('ContabilidadFinanzas.Periodos.index'))
                        }
                    }
                ]
            },
            {
                label: 'Marketing',
                icon: 'bi bi-megaphone-fill'
            },
            {
                label: 'Producción',
                icon: 'bi bi-nut-fill',
            },
            {
                label: 'Recursos Humanos',
                icon: 'bi bi-people-fill',
                items: [
                    {
                        label: 'Nóminas',
                        icon: '',
                        command: () => {
                            router.visit(route('RecursosHumanos.Nomina.index'))
                         }
                    },
                ]
            }
        ]
    })
})