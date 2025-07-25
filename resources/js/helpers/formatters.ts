// resources/js/helpers/formatters.ts

export function formatCpfCnpj(value: string | null): string {
    if (!value) {
        return 'N/A';
    }
    const cleanedValue = value.replace(/\D/g, ''); // Remove tudo que não for dígito

    if (cleanedValue.length === 11) {
        // CPF: 000.000.000-00
        return cleanedValue.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    } else if (cleanedValue.length === 14) {
        // CNPJ: 00.000.000/0000-00
        return cleanedValue.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
    }
    return value; // Retorna o valor original se não for nem CPF nem CNPJ
}

export function formatPhone(value: string | null): string {
    if (!value) {
        return 'N/A';
    }
    const cleanedValue = value.replace(/\D/g, ''); // Remove tudo que não for dígito

    if (cleanedValue.length === 11) {
        // Celular (com 9º dígito): (00) 00000-0000
        return cleanedValue.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else if (cleanedValue.length === 10) {
        // Telefone fixo/Celular antigo: (00) 0000-0000
        return cleanedValue.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
    }
    return value; // Retorna o valor original se não for um formato reconhecido
}