<script setup lang="js">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardHeader, CardContent, CardTitle } from "@/components/ui/card";
import { Head, Link } from '@inertiajs/vue3';
import { Button, buttonVariants } from '@/components/ui/button';
import { deleteProduct } from '@/composables/useProduct';

defineProps({
    product: {
        type: Object,
        required: true
    }
})

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Manage Products',
        href: '/products',
    },
    {
        title: 'Product detail',
        href: '/products',
    },
];

</script>

<template>

    <Head title="Product detail" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 items-center overflow-x-auto">
            <div class="flex w-full max-w-2xl flex-col">
                <Card class="mt-3">
                    <CardHeader>
                        Products detail
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Name</div>
                            <div>{{ product.name }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Category</div>
                            <div>{{ product.category.name }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Brand</div>
                            <div>{{ product.brand }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Price</div>
                            <div>${{ product.price /100 }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Weight</div>
                            <div>{{ product.weight }}</div>
                        </div>
                        <div class="flex space-x-4">
                            <div class="w-32 font-semibold">Description</div>
                            <div class="flex-1">{{ product.description }}</div>
                        </div>
                        <div class="flex justify-between items-center space-x-4 mt-6">
                            <Link :href="route('products.index')" :class="buttonVariants({variant: 'outline'})">Back</Link>
                            <div>
                                <Link :href="route('products.edit', product)" :class="buttonVariants({variant: 'default'})">Edit</Link>
                                <Button class="ml-2" variant="destructive" @click="deleteProduct(product.id)">Delete</Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
