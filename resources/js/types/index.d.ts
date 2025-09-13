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
    id?: string;
    teks: string;
    skor: number | null;
}

export interface Pertanyaan {
    id?: string;
    teks: string;
    tipe: 'text' | 'radio' | 'checkbox' | 'number' | 'date';
    kuesioner?: {
        id: string;
        judul: string;
        deskripsi: string;
    };
    unsur: string;
    opsi: Opsi[];
}

export interface Responden {
    id?: string;
    name: string;
    jk: 'Laki-Laki' | 'Perempuan';
    umur?: number;
    pekerjaan?: string;
    pendidikan_terakhir?: string;
    kuesioner_id: number;
    kuesioner_judul: string;
    jawaban?: Jawaban[];
    kritik: string | null;
    saran: string | null;
    created_at: string;
    updated_at: string;
}

export interface Jawaban {
    id?: string;
    pertanyaan_id: string;
    respondens_id: string;
    teks: string | null;
    angka: number | null;
    tanggal: string | null;
    opsi_id: number | null;
    opsiJawaban?: OpsiJawaban;
    pertanyaan: Pertanyaan;
    created_at: string;
    updated_at: string;
}

// Kritik & Saran hanya ambil sebagian field responden
export interface KritikSaran {
    id: string; // UUID
    name: string;
    jk: string;
    kritik?: string | null;
    saran?: string | null;
    created_at?: string; // optional, kalau mau dipakai
    showKritikFull?: boolean;
    showSaranFull?: boolean;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    kuesioners: Kuesioner[];
    kuesioner: Kuesioner;
    respondens: Responden[];
    pertanyaans: Pertanyaan[];
    responden?: Responden;
    jawabans: Jawaban[];
    currentKuesionerId: number | null;
    mode: string;
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
