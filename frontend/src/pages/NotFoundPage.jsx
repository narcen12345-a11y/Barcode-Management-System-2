import { Link } from 'react-router-dom';
import { Home } from 'lucide-react';

export function NotFoundPage() {
  return (
    <div className="flex min-h-screen flex-col items-center justify-center bg-slate-950 px-4 text-center">
      <h1 className="text-7xl font-bold text-cyan-400">404</h1>
      <p className="mt-4 text-xl font-semibold text-slate-200">Halaman tidak ditemukan</p>
      <p className="mt-2 text-sm text-slate-400">
        Halaman yang Anda cari mungkin telah dipindahkan atau tidak tersedia.
      </p>
      <Link
        to="/"
        className="mt-6 inline-flex items-center gap-2 rounded-lg bg-cyan-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-cyan-600"
      >
        <Home className="h-4 w-4" />
        Kembali ke Dashboard
      </Link>
    </div>
  );
}
