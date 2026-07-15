import { Spinner } from './Spinner';

export function Loading({ message = 'Memuat data...' }) {
  return (
    <div className="flex flex-col items-center justify-center gap-3 py-20">
      <Spinner size="lg" />
      <p className="text-sm text-slate-400">{message}</p>
    </div>
  );
}
