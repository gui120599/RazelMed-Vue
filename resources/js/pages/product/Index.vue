<script setup lang="js">
import AppLayout from '@/layouts/AppLayout.vue';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table';
import { Head, Link } from '@inertiajs/vue3';
import { Button, buttonVariants } from '@/components/ui/button';
import { deleteProduct } from '@/composables/useProduct';


defineProps({
    products: {
        type: Array,
        required: true
    }
})


const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Products',
        href: '/products',
    }
];

</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="mt-3">
                <Link :href="route('products.create')" :class="buttonVariants({ variant: 'outline' })">Add Product
                </Link>
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Name</TableHead>
                        <TableHead>Category</TableHead>
                        <TableHead>Price</TableHead>
                        <TableHead>Weight</TableHead>
                        <TableHead class="w-[120px]">Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="product in products" :key="product.id">
                        <TableCell>{{ product.name }}</TableCell>
                        <TableCell>{{ product.category.name }}</TableCell>
                        <TableCell>${{ product.price / 100 }}</TableCell>
                        <TableCell>{{ product.weight }}</TableCell>
                        <TableCell class="space-x-2">
                            <Link :href="route('products.show', product)"
                                :class="buttonVariants({ variant: 'secondary' })">Show</Link>
                                <Link :href="route('products.edit', product)"
                                :class="buttonVariants({ variant: 'default' })">Edit</Link>
                            <Button variant="destructive" @click="deleteProduct(product.id)">Delete</Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>
