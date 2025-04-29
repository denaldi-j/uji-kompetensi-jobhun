<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { debounce } from "lodash"; 
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { toast } from 'vue-sonner'
import AppLayout from '@/layouts/AppLayout.vue';
import { EllipsisVertical } from 'lucide-vue-next';

import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'

import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'

import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationFirst,
  PaginationLast,
  PaginationNext,
  PaginationPrevious,
} from '@/components/ui/pagination'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuRadioGroup,
  DropdownMenuRadioItem,
  DropdownMenuSeparator,
  DropdownMenuShortcut,
  DropdownMenuSub,
  DropdownMenuSubContent,
  DropdownMenuSubTrigger,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Daftar Buku',
        href: '/buku',
    },
];

const props = defineProps({
    buku: Object,
    searchTerm: String
});

const search = ref(props.searchTerm);

const form = useForm({
    judul: '',
    penulis: '',
    penerbit: '',
    tahun_terbit: 2025,
    stok: 1,
});

const submit = () => {
    let url;
    if (isEditMode.value && selectedRow.value) {
        url = route('buku.update', selectedRow.value.id);
    } else {
        url = route('buku.store');
    }
    form.post(url, {
        onSuccess: (res) => {
            toast('Success', {
                description: 'Buku berhasil disimpan!',
            })
            isDialogOpen.value = false;
        },
        onError: async (errors) => {
            console.log(errors);
        }
    });
};

watch(
    search, debounce(
        (q) => router.get(route('buku.index'), { search: q }, {
            preserveState: true,
            preserveScroll: true,
        }), 500
    )
);

const isDialogOpen = ref(false);
const isEditMode = ref(false);
const selectedRow = ref<any>(null);
const isConfirmDialogOpen = ref(false);
const rowToDelete = ref<any>(null);

function handleEdit(row: any) {
    isEditMode.value = true;
    selectedRow.value = row;
    // Fill form with row data
    form.judul = row.judul;
    form.penulis = row.penulis;
    form.penerbit = row.penerbit;
    form.tahun_terbit = row.tahun_terbit;
    form.stok = row.stok;
    isDialogOpen.value = true;
}

function handleDelete(row: any) {
    rowToDelete.value = row;
    isConfirmDialogOpen.value = true;
}

function confirmDelete() {
    if (!rowToDelete.value) return;
    router.post(route('buku.delete', rowToDelete.value.id), {
        onSuccess: (res) => {
            toast('Success', { description: 'Buku berhasil dihapus!' });
        },
        onError: (errors) => {
            toast('Error', { description: 'Gagal menghapus buku.' });
        }
    });

    isConfirmDialogOpen.value = false;
    rowToDelete.value = null;
}

</script>

<template>
    <Head title="Buku" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative p-4 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <div class="flex items pb-4 justify-between">
                    <Input type="text" v-model="search" placeholder="Cari judul buku..." class="w-1/3" />
                    <Button variant="outline" @click="
                        form.reset();
                        isDialogOpen = true;
                        isEditMode = false;
                        selectedRow = null;
                    ">
                        Tambah Buku
                    </Button>
                </div>
                <Table :class="'w-full border rounded-xl'">
                    <TableHeader :class="`bg-gray-100 font-medium dark:bg-gray-800 dark:text-gray-100`">
                        <TableRow>
                            <TableHead>Judul</TableHead>
                            <TableHead>Penulis</TableHead>
                            <TableHead>Penerbit</TableHead>
                            <TableHead>Tahun Terbit</TableHead>
                            <TableHead class="text-center">Stok</TableHead>
                            <TableHead class="text-center w-[50px]"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="item in buku.data" :key="item.id">
                            <TableCell class="font-medium">{{ item.judul }}</TableCell>
                            <TableCell>{{ item.penulis }}</TableCell>
                            <TableCell>{{ item.penerbit }}</TableCell>
                            <TableCell>{{ item.tahun_terbit }}</TableCell>
                            <TableCell class="text-center">{{ item.stok }}</TableCell>
                            <TableCell class="text-center">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                    <Button
                                        variant="ghost"
                                        class="flex h-8 w-8 p-0 data-[state=open]:bg-muted"
                                    >
                                        <EllipsisVertical class="h-4 w-4" />
                                        <span class="sr-only">Open menu</span>
                                    </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end" class="w-[160px]">
                                        <DropdownMenuItem @click.prevent="handleEdit(item)">Edit</DropdownMenuItem>
                                        <DropdownMenuItem @click="handleDelete(item)" class="text-red-500">Delete</DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="buku.data.length === 0">
                            <TableCell colspan="5" class="text-center">
                                <p class="text-sm text-gray-500 dark:text-neutral-500 dark:hover:text-blue-500 dark:focus:text-blue-500">
                                    Tidak ada data
                                </p>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
                <div class="flex pt-3 items-center justify-between">
                    <Pagination :items-per-page="buku.per_page" :total="buku.total" :default-page="buku.current_page">
                        <PaginationContent class="flex items-center justify-left">
                            <PaginationPrevious />
                            <PaginationEllipsis :index="4" />
                            <PaginationNext />
                        </PaginationContent>
                        
                    </Pagination>
                    <p class="text-sm mr-2 text-gray-500 dark:text-neutral-500 dark:hover:text-blue-500 dark:focus:text-blue-500">
                        Showing {{ buku.from??0 }} to {{ buku.to??0 }} of {{ buku.total }} results
                    </p>
                </div>
            </div>
        </div>

        <Dialog v-model:open="isDialogOpen">
            <DialogContent class="sm:max-w-[40rem]">
                <DialogHeader>
                    <DialogTitle>Form Buku</DialogTitle>
                    <DialogDescription>
                    {{ isEditMode ? 'Edit' : 'Tambah' }} buku ke dalam sistem.
                    </DialogDescription>
                </DialogHeader>
                <div class="grid gap-4 pt-4">
                    <form class="" @submit.prevent="submit">
                        <div class="mb-4">
                            <Label class="mb-2">Judul Buku</Label>
                            <Input type="text" placeholder="Judul buku" v-model="form.judul" required />
                        </div>
                        <div class="mb-4">
                            <Label class="mb-2">Penulis</Label>
                            <Input type="text" v-model="form.penulis" required />
                        </div>
                        <div class="mb-4">
                            <Label class="mb-2">Penerbit</Label>
                            <Input type="text" v-model="form.penerbit" required />
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <Label class="mb-2">Tahun Terbit</Label>
                                <Input type="number" max="2025" min="1900" v-model="form.tahun_terbit" required />
                            </div>
                            <div>
                                <Label class="mb-2">Stok</Label>
                                <Input type="number" max="9999" min="1" v-model="form.stok" required />
                            </div>
                        </div>
                        
                        <Button type="submit">{{ isEditMode ? 'Update' : 'Simpan' }} Buku</Button>
                    </form>
                </div>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="isConfirmDialogOpen">
            <DialogContent>
                <DialogHeader>
                <DialogTitle>Konfirmasi Hapus</DialogTitle>
                <DialogDescription>
                    Apakah Anda yakin ingin menghapus buku <b>{{ rowToDelete?.judul }}</b>?
                </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                <Button variant="outline" @click.prevent="isConfirmDialogOpen = false">Batal</Button>
                <Button variant="destructive" @click="confirmDelete">Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
        
    </AppLayout>
</template>
