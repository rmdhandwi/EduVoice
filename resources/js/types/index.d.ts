import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface Flash {
    success?: string;
    error?: string;
    warning?: string;
    info?: string;
}

export interface Opsi {
    text: string;
    skor: number | null;
}

export interface Pertanyaan {
    id?: number;
    teks: string;
    tipe: 'text' | 'radio' | 'checkbox';
    opsi: Opsi[];
}

export interface Kuesioner {
    id: number;
    judul: string;
    deskripsi?: string;
    pertanyaans?: Pertanyaan[];
    created_at: string;
    updated_at: string;
}


export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    kuesioners: Kuesioner[];
    kuesioner: Kuesioner;
    flash: Flash;
    ziggy: Config & { location: string };
};

export interface User {
    id: number;
    name: string;
    username: string;
    role: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}
