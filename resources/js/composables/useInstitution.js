// resources/js/composables/useInstitution.js
import { router } from '@inertiajs/vue3'; // Já usa 'router'
import { toast } from 'vue-sonner';

export function deleteInstitution(institutionId) { // Renomeado para deleteInstitution e recebe institutionId
    if (confirm("Are you sure you want to delete this institution?")) { // Mensagem mais específica
        router.delete(route('institutions.destroy', institutionId), { // Rotas e parâmetros para institutions
            preserveScroll: true,
            onSuccess: () => toast.success('Institution deleted successfully.') // Mensagem de sucesso para institutione
        });
    }
}