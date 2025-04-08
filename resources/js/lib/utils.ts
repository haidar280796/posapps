import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function formatRupiah(amountValue : number) {
    if (!amountValue) return "Rp 0";

    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(amountValue);
}

export function formatRibu(amountValue : number) {
    if (!amountValue) return "0";

    return Number(amountValue).toLocaleString("id-ID");
}

