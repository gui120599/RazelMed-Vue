// resources/js/composables/useDashboard.js
import { router } from '@inertiajs/vue3'; // Já usa 'router'
import { toast } from 'vue-sonner';

export function deleteDashboard(dashboardId) { // Renomeado para deleteDashboard e recebe dashboardId
    if (confirm("Are you sure you want to delete this dashboard?")) { // Mensagem mais específica
        router.delete(route('dashboards.destroy', dashboardId), { // Rotas e parâmetros para dashboards
            preserveScroll: true,
            onSuccess: () => toast.success('Dashboard deleted successfully.') // Mensagem de sucesso para dashboarde
        });
    }
}