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
import { EllipsisVertical, CalendarIcon } from 'lucide-vue-next';
import { cn } from '@/lib/utils';
import { Calendar } from '@/components/ui/calendar'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import {
  DateFormatter,
  type DateValue,
  getLocalTimeZone,
} from '@internationalized/date';

const df = new DateFormatter('en-US', {
  dateStyle: 'long',
})

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
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

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
        title: 'Daftar Peminjaman Buku',
        href: '/peminjaman',
    },
];

const props = defineProps({
    peminjaman: Object,
    pengguna: Object,
    buku: Object,
    searchTerm: String
});

const search = ref(props.searchTerm);

const form = useForm({
    buku_id: '',
    pengguna_id: '',
    tanggal_pinjam: '',
    tanggal_kembali: '',
    status: 1,
});

const formPengembalian = useForm({
    peminjaman_id: '',
    tanggal_pengembalian: '',
    denda: 0,
});

const submitPengembalian = () => {
    formPengembalian.post(route('peminjaman.update'), {
        onSuccess: (res) => {
            toast('Success', {
                description: 'Buku berhasil dikembalikan!',
            })
            isConfirmDialogOpen.value = false;
        },
        onError: async (errors) => {
            console.log(errors);
        }
    });
};

const submit = () => {
    let url;
    if (isEditMode.value && selectedRow.value) {
        url = route('peminjaman.update', selectedRow.value.id);
    } else {
        url = route('peminjaman.store');
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
        (q) => router.get(route('peminjaman.index'), { search: q }, {
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
const startDate = ref<DateValue>();
const endDate = ref<DateValue>();
const date_value = ref<DateValue>();

function handlePengembalian(row: any) {
    rowToDelete.value = row;
    formPengembalian.peminjaman_id = row.id;
    isConfirmDialogOpen.value = true;
}

function handleStartDateChange(date: DateValue) {
    startDate.value = date;
    form.tanggal_pinjam = date ? date.toString() : '';
}

function handleDefaultDateChange(date: DateValue) {
    date_value.value = date;
    formPengembalian.tanggal_pengembalian = date ? date.toString() : '';
}

function handleEndDateChange(date: DateValue) {
    endDate.value = date;
    form.tanggal_kembali = date ? date.toString() : '';
}

</script>

<template>
    <Head title="Peminjaman Buku" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative p-4 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <div class="flex items pb-4 justify-between">
                    <Input type="text" v-model="search" placeholder="Cari buku..." class="w-1/3" />
                    <Button variant="outline" @click="
                        form.reset();
                        isDialogOpen = true;
                        isEditMode = false;
                        selectedRow = null;
                    ">
                        Buat Peminjaman
                    </Button>
                </div>
                <Table :class="'w-full border rounded-xl'">
                    <TableHeader :class="`bg-gray-100 font-medium dark:bg-gray-800 dark:text-gray-100`">
                        <TableRow>
                            <TableHead>Nama Peminjaman</TableHead>
                            <TableHead>Judul Buku</TableHead>
                            <TableHead>Tanggal Pinjam</TableHead>
                            <TableHead>Tanggal Kembali</TableHead>
                            <TableHead>Tanggal Pengembalian</TableHead>
                            <TableHead>Denda</TableHead>
                            <TableHead class="text-center">Status</TableHead>
                            <TableHead class="text-center w-[50px]"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="item in peminjaman.data" :key="item.id">
                            <TableCell>{{ item.pengguna.nama }}</TableCell>
                            <TableCell>{{ item.buku.judul }}</TableCell>
                            <TableCell>{{ item.tanggal_pinjam }}</TableCell>
                            <TableCell>{{ item.tanggal_kembali }}</TableCell>
                            <TableCell>{{ item.pengembalian?.tanggal_pengembalian }}</TableCell>
                            <TableCell>{{ item.pengembalian?.denda }}</TableCell>
                            <TableCell class="text-center">{{ item.status }}</TableCell>
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
                                        <DropdownMenuItem @click.prevent="handlePengembalian(item)">Pengembalian</DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="peminjaman.data.length === 0">
                            <TableCell colspan="5" class="text-center">
                                <p class="text-sm text-gray-500 dark:text-neutral-500 dark:hover:text-blue-500 dark:focus:text-blue-500">
                                    Tidak ada data
                                </p>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
                <div class="flex pt-3 items-center justify-between">
                    <Pagination :items-per-page="peminjaman.per_page" :total="peminjaman.total" :default-page="peminjaman.current_page">
                        <PaginationContent class="flex items-center justify-left">
                            <PaginationPrevious />
                            <PaginationEllipsis :index="4" />
                            <PaginationNext />
                        </PaginationContent>
                        
                    </Pagination>
                    <p class="text-sm mr-2 text-gray-500 dark:text-neutral-500 dark:hover:text-blue-500 dark:focus:text-blue-500">
                        Showing {{ peminjaman.from??0 }} to {{ peminjaman.to??0 }} of {{ peminjaman.total }} results
                    </p>
                </div>
            </div>
        </div>

        <Dialog v-model:open="isDialogOpen">
            <DialogContent class="sm:max-w-[40rem]">
                <DialogHeader>
                    <DialogTitle>Form Pinjaman</DialogTitle>
                    <DialogDescription>
                    {{ isEditMode ? 'Edit' : 'Tambah' }} buku ke dalam sistem.
                    </DialogDescription>
                </DialogHeader>
                <div class="grid gap-4 pt-4">
                    <form class="" @submit.prevent="submit">
                        <div class="mb-4">
                            <Label class="mb-2">Pilih Pengguna</Label>
                            <Select v-model="form.pengguna_id">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Pilihan..." />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem v-for="item in pengguna" :value="item.id" :key="item.id">
                                            {{ item.nama }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="mb-4">
                            <Label class="mb-2">Pilih Buku</Label>
                            <Select v-model="form.buku_id">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Pilihan..." />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem v-for="item in buku" :value="item.id" :key="item.id">
                                            {{ item.judul }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-5">
                            <div>
                                <Label class="mb-2">Tanggal Pinjam</Label>
                                <Popover>
                                    <PopoverTrigger as-child>
                                        <Button
                                            variant="outline"
                                            :class="cn(
                                            'w-[280px] justify-start text-left font-normal',
                                            !startDate && 'text-muted-foreground'
                                            )"
                                        >
                                            <CalendarIcon class="mr-2 h-4 w-4" />
                                            {{ startDate ? df.format(startDate.toDate(getLocalTimeZone())) : "Pick a date" }}
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-auto p-0">
                                        <Calendar 
                                            v-model="startDate"
                                            @update:model-value="handleStartDateChange"
                                            initial-focus 
                                        />
                                    </PopoverContent>
                                </Popover>
                            </div>
                            <div>
                                <Label class="mb-2">Tanggal Kembali</Label>
                                <Popover>
                                    <PopoverTrigger as-child>
                                        <Button
                                            variant="outline"
                                            :class="cn(
                                            'w-[280px] justify-start text-left font-normal',
                                            !endDate && 'text-muted-foreground'
                                            )"
                                        >
                                            <CalendarIcon class="mr-2 h-4 w-4" />
                                            {{ endDate ? df.format(endDate.toDate(getLocalTimeZone())) : "Pick a date" }}
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-auto p-0">
                                        <Calendar 
                                            v-model="endDate"
                                            @update:model-value="handleEndDateChange"
                                            initial-focus 
                                        />
                                    </PopoverContent>
                                </Popover>
                            </div>
                        </div>
                        
                        <Button type="submit">Buat Pinjaman</Button>
                    </form>
                </div>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="isConfirmDialogOpen">
            <DialogContent>
                <DialogHeader>
                <DialogTitle>Pengembalian Buku</DialogTitle>
                <DialogDescription>
                    Pengembalian buku oleh <b>{{ rowToDelete?.pengguna?.nama }}</b>, judul buku <b>"{{ rowToDelete?.buku?.judul }}"</b>
                </DialogDescription>
                <DialogDescription>
                    Peminjaman Tanggal: <b>{{ rowToDelete?.tanggal_pinjam }}</b><br>
                    Tanggal Kembali: <b>{{ rowToDelete?.tanggal_kembali }}</b>
                </DialogDescription>
                </DialogHeader>
                

                <div class="grid gap-4">
                    <form @submit.prevent="submitPengembalian">
                            <Input type="hidden" v-model="formPengembalian.peminjaman_id" /> 
                            <div class="mb-4">
                                <Label class="mb-2">Tanggal Pengembalian</Label>
                                <Popover>
                                    <PopoverTrigger as-child>
                                        <Button
                                            variant="outline"
                                            :class="cn(
                                            'w-[280px] justify-start text-left font-normal',
                                            !date_value && 'text-muted-foreground'
                                            )"
                                        >
                                            <CalendarIcon class="mr-2 h-4 w-4" />
                                            {{ date_value ? df.format(date_value.toDate(getLocalTimeZone())) : "Pick a date" }}
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-auto p-0">
                                        <Calendar 
                                            v-model="date_value"
                                            @update:model-value="handleDefaultDateChange"
                                            initial-focus 
                                        />
                                    </PopoverContent>
                                </Popover>
                            </div>
                            <div>
                                <Label class="mb-2">Denda</Label>
                                <Input type="number" v-model="formPengembalian.denda" placeholder="Denda" class="w-[280px] mb-4" />
                            </div>
                        
                        <Button type="submit">Simpan Pengembalian</Button>
                    </form>
                </div>
                <DialogFooter>
                    
                </DialogFooter>
            </DialogContent>
        </Dialog>
        
    </AppLayout>
</template>
