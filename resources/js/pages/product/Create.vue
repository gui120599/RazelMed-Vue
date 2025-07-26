<script setup lang="js">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardHeader, CardContent, CardTitle } from "@/components/ui/card";
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button, buttonVariants } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { toast } from 'vue-sonner';

defineProps({
    categories:{
        type: Array,
        required: true
    }
})

const form = useForm({
    name: '',
    brand: '',
    category_id: '',
    description: '',
    price: null,
    weight: null
})

const handleSubmit = () => {
    form.post(route('products.store'),{
        preserveScroll: true,
        onSuccess: () => toast.success('Product saved successfully.')
    })
}


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
        title: 'Add Product',
        href: '/products',
    },
];

</script>

<template>

    <Head title="Add Product" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 items-center overflow-x-auto">
            <div class="flex w-full max-w-2xl flex-col">
                <Card class="mt-3">
                    <CardHeader>
                        Add new Product
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <form class="space-y-6" @submit.prevent="handleSubmit">
                            <div class="grid w-full gap-2">
                                <Label for="name">Name</Label>
                                <Input id="name" v-model="form.name" />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div class="grid w-full gap-2">
                                    <Label for="brand">Brand</Label>
                                    <Input id="brand" v-model="form.brand" />
                                    <InputError :message="form.errors.brand" />
                                </div>
                                <div class="grid w-full gap-2">
                                    <Label for="category_id">Category</Label>
                                    <Select id="category_id" v-model="form.category_id">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Select a category"></SelectValue>
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <InputError :message="form.errors.category_id" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div class="grid w-full gap-2">
                                    <Label for="price">Price</Label>
                                    <Input id="price" v-model="form.price" />
                                    <InputError :message="form.errors.price" />
                                </div>
                                <div class="grid w-full gap-2">
                                    <Label for="weight">Weight</Label>
                                    <Input id="weight" v-model="form.weight" />
                                    <InputError :message="form.errors.weight" />
                                </div>
                            </div>
                            <div class="grid w-full gap-2">
                                <Label for="description">Description</Label>
                                <Textarea id="description" v-model="form.description" />
                                <InputError :message="form.errors.description" />
                            </div>
                            <div class="flex justify-between items-center">
                                <Button variant="default" :disabled="form.processing">Save Product</Button>
                                <Link :class="buttonVariants({ variant: 'ghost' })" :href="route('products.index')">
                                Cancel
                                </Link>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
