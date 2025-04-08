import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    flash: {
        error?: string;
        success?: string;
    };
    paymentUrl: string;
    submitedinvoice: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Category {
    id: number;
    nama_kategori: string;
    slug: string;
    image?: string;
    created_at: string;
    updated_at: string;
}

export interface Customer {
    id: number;
    nama: string;
    email?: string;
    telepon?: string;
    alamat?: string;
    created_at: string;
    updated_at: string;
}

export interface Product {
    id: string | number;
    kode_produk: string;
    nama_produk: string;
    kategori_id: number;
    satuan_dasar_id: string | number;
    satuan_dasar?: Unit;
    barcode?: string;
    harga_beli: number;
    harga_jual: number;
    deskripsi: string;
    image?: string;
    created_at: string;
    updated_at: string;
}

export interface Unit {
    id: number;
    nama_satuan: string;
    created_at: string;
    updated_at: string;
}

export interface ProductPrice {
    id?: number | string;
    product_id?: number | string;
    warehouse_id: number | string;
    satuan_id: number | string;
    satuan?: Unit;
    konversi: number | string;
    harga_beli: number | string;
    harga_jual: number | string;
    barcode?: string;
    created_at?: string;
    updated_at?: string;
}

enum WarehouseType {
    'gudang',
    'toko',
}
export interface Warehouse {
    id: number | string;
    nama_gudang: string;
    lokasi?: string;
    phone?: string;
    tipe: WarehouseType;
    created_at?: string;
    updated_at?: string;
}

export interface Stock {
    id: number | string;
    warehouse_id: number | string;
    warehouse?: Warehouse;
    product_id: number | string;
    product?: Product;
    jumlah: number;
    created_at?: string;
    updated_at?: string;
}

enum AdjustmentType {
    'expired',
    'transfer',
    'lost',
    'damaged'
}

export interface StockAdjustment {
    id: number | string;
    warehouse_id: number | string;
    warehouse?: Warehouse;
    warehouse_target_id: number | string;
    warehouseTarget?: Warehouse;
    product_id: number | string;
    product?: Product;
    user_id: number | string;
    User: User;
    adjustment_type: AdjustmentType;
    jumlah: number;
    reason?: string;
    created_at?: string;
    updated_at?: string;
}

export interface StockTransaction {
    id: number;
    user_id: number;
    warehouse_id: number;
    warehouse?: Warehouse;
    product_id: number;
    product?: Product;
    jumlah: number;
    satuan_id: number | string;
    satuan?: Unit;
    created_at?: string;
    updated_at?: string;
}

export interface StockAdjustmentTransaction {
    id: number;
    user_id: number;
    warehouse_id: number;
    warehouse?: Warehouse;
    product_id: number;
    product?: Product;
    adjustment_type: AdjustmentType;
    jumlah: number;
    satuan_id: number | string;
    satuan?: Unit;
    reason?: string;
    created_at?: string;
    updated_at?: string;
}

interface PaginationSelect2 {
    current_page: number;
    last_page: number;
}

export interface ResponseSelect2 {
    data: Array<Object | any>;
    pagination: PaginationSelect2;
}

type ProductPayload = Pick<Product, 'id' | 'kode_produk' | 'nama_produk' | 'barcode' | 'harga_beli' | 'harga_jual' | 'satuan_dasar_id'>;
type ProductPricePayload = Pick<ProductPrice, 'id' | 'product_id' | 'warehouse_id' | 'satuan_id' | 'konversi' | 'harga_beli' | 'harga_jual' | 'barcode' | 'satuan'>;

export interface ProductPosPrice extends ProductPayload {
    harga_produk: Array<ProductPricePayload>;
    stock: Stock;
}
export interface ItemCart extends ProductPayload {
    cart_id: number;
    quantity: number;
    satuan_cart: string | number;
    harga_produk: Array<ProductPricePayload>;
    stock: Stock;
}

export type BreadcrumbItemType = BreadcrumbItem;
