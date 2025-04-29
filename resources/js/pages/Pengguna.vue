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
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

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
        title: 'Daftar Pengguna',
        href: '/pengguna',
    },
];

const props = defineProps({
    pengguna: Object,
    searchTerm: String
});

const search = ref(props.searchTerm);

const form = useForm({
    nama: '',
    jenis_pengguna: '',
    alamat: '',
    no_telp: '',
});

const submit = () => {
    let url;
    if (isEditMode.value && selectedRow.value) {
        url = route('pengguna.update', selectedRow.value.id);
    } else {
        url = route('pengguna.store');
    }
    form.post(route('pengguna.store'), {
        onSuccess: (res) => {
            toast('Success', {
                description: 'Pengguna berhasil disimpan!',
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
        (q) => router.get(route('pengguna.index'), { search: q }, {
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
    form.nama = row.nama;
    form.jenis_pengguna = row.jenis_pengguna;
    form.alamat = row.alamat;
    form.no_telp = row.no_telp;
    isDialogOpen.value = true;
}

function handleDelete(row: any) {
    rowToDelete.value = row;
    isConfirmDialogOpen.value = true;
}

function confirmDelete() {
    if (!rowToDelete.value) return;
    router.post(route('pengguna.delete', rowToDelete.value.id), {
        onSuccess: (res) => {
            toast('Success', { description: 'Pengguna berhasil dihapus!' });
        },
        onError: (errors) => {
            toast('Error', { description: 'Gagal menghapus pengguna.' });
        }
    });

    isConfirmDialogOpen.value = false;
    rowToDelete.value = null;
}

</script>

<template>
    <Head title="Pengguna" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative p-4 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <div class="flex items pb-4 justify-between">
                    <Input type="text" v-model="search" placeholder="Cari pengguna..." class="w-1/3" />
                    <Button variant="outline" @click="
                        form.reset();
                        isDialogOpen = true;
                        isEditMode = false;
                        selectedRow = null;
                    ">
                        Tambah Pengguna
                    </Button>
                </div>
                <Table :class="'w-full border rounded-xl'">
                    <TableHeader :class="`bg-gray-100 font-medium dark:bg-gray-800 dark:text-gray-100`">
                        <TableRow>
                            <TableHead>Nama Pengguna</TableHead>
                            <TableHead>Jenis Pengguna</TableHead>
                            <TableHead>Alamat</TableHead>
                            <TableHead>No. Telepon</TableHead>
                            <TableHead class="text-center w-[50px]"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="item in pengguna.data" :key="item.id">
                            <TableCell class="font-medium">{{ item.nama }}</TableCell>
                            <TableCell>{{ item.jenis_pengguna }}</TableCell>
                            <TableCell>{{ item.alamat }}</TableCell>
                            <TableCell>{{ item.no_telp }}</TableCell>
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
                        <TableRow v-if="pengguna.data.length === 0">
                            <TableCell colspan="5" class="text-center">
                                <p class="text-sm text-gray-500 dark:text-neutral-500 dark:hover:text-blue-500 dark:focus:text-blue-500">
                                    Tidak ada data
                                </p>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
                <div class="flex pt-3 items-center justify-between">
                    <Pagination :items-per-page="pengguna.per_page" :total="pengguna.total" :default-page="pengguna.current_page">
                        <PaginationContent class="flex items-center justify-left">
                            <PaginationPrevious />
                            <PaginationEllipsis :index="4" />
                            <PaginationNext />
                        </PaginationContent>
                        
                    </Pagination>
                    <p class="text-sm mr-2 text-gray-500 dark:text-neutral-500 dark:hover:text-blue-500 dark:focus:text-blue-500">
                        Showing {{ pengguna.from??0 }} to {{ pengguna.to??0 }} of {{ pengguna.total }} results
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
                            <Label class="mb-2">Nama Pengguna</Label>
                            <Input type="text" placeholder="Nama pengguna" v-model="form.nama" required />
                        </div>
                        <div class="mb-4">
                            <Label class="mb-2">Jenis Pengguna</Label>
                            <Select v-model="form.jenis_pengguna">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Pilihan..." />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem value="mahasiswa">
                                        Mahasiswa
                                        </SelectItem>
                                        <SelectItem value="dosen">
                                        Dosen
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="mb-4">
                            <Label class="mb-2">Alamat</Label>
                            <Input type="text" v-model="form.alamat" required />
                        </div>
                        <div class="mb-4">
                            <Label class="mb-2">No. Telepon</Label>
                            <Input type="text" v-model="form.no_telp" required />
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
                    Apakah Anda yakin ingin menghapus pengguna <b>{{ rowToDelete?.nama }}</b>?
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
