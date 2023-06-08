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
                        label: 'Esquemas de mayores',
                        icon: 'bi bi-table',
                        command: () => {
                            router.visit(route('ContabilidadFinanzas.EsquemasMayor.index'))
                        }
                    },
                    {
                        label: 'Balanza de comprobación',
                        icon: 'bi bi-file-earmark-ruled',
                        command: () => {
                            router.visit(route('ContabilidadFinanzas.BalanzaComprobacion.index'))
                        }
                    },
                    {
                        label: 'Balance general',
                        icon: 'bi bi-file-earmark-spreadsheet',
                        command: () => {
                            router.visit(route('ContabilidadFinanzas.BalanceGeneral.index'))
                        }
                    },
                    {
                        label: 'Estado de resultados',
                        icon: 'bi bi-file-diff-fill',
                        command: () => {
                            router.visit(route('ContabilidadFinanzas.EstadoResultados.index'))
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
                icon: 'bi bi-nut-fill'
            },
            {
                label: 'Recursos Humanos',
                icon: 'bi bi-people-fill'
            }
        ]
    })
})