import { ChevronLeft, ChevronRight } from 'lucide-react';
import { cn } from '../../utils/cn';

export function Pagination({ meta, onPageChange }) {
  if (!meta || meta.last_page <= 1) return null;

  const { current_page, last_page, total, per_page } = meta;
  const from = (current_page - 1) * per_page + 1;
  const to = Math.min(current_page * per_page, total);

  const pages = [];
  const maxVisible = 5;
  let start = Math.max(1, current_page - Math.floor(maxVisible / 2));
  let end = Math.min(last_page, start + maxVisible - 1);

  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1);
  }

  for (let i = start; i <= end; i++) {
    pages.push(i);
  }

  return (
    <div className="flex flex-col items-center gap-3 sm:flex-row sm:justify-between">
      <p className="text-sm text-slate-400">
        Menampilkan {from}–{to} dari {total}
      </p>
      <div className="flex items-center gap-1">
        <button
          onClick={() => onPageChange(current_page - 1)}
          disabled={current_page <= 1}
          className="rounded-lg p-2 text-slate-400 transition hover:bg-slate-800 hover:text-slate-200 disabled:cursor-not-allowed disabled:opacity-50"
        >
          <ChevronLeft className="h-4 w-4" />
        </button>
        {start > 1 && (
          <>
            <button
              onClick={() => onPageChange(1)}
              className="rounded-lg px-3 py-1.5 text-sm text-slate-400 transition hover:bg-slate-800 hover:text-slate-200"
            >
              1
            </button>
            {start > 2 && <span className="px-1 text-slate-600">...</span>}
          </>
        )}
        {pages.map((page) => (
          <button
            key={page}
            onClick={() => onPageChange(page)}
            className={cn(
              'rounded-lg px-3 py-1.5 text-sm transition',
              page === current_page
                ? 'bg-cyan-500 text-white'
                : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200'
            )}
          >
            {page}
          </button>
        ))}
        {end < last_page && (
          <>
            {end < last_page - 1 && <span className="px-1 text-slate-600">...</span>}
            <button
              onClick={() => onPageChange(last_page)}
              className="rounded-lg px-3 py-1.5 text-sm text-slate-400 transition hover:bg-slate-800 hover:text-slate-200"
            >
              {last_page}
            </button>
          </>
        )}
        <button
          onClick={() => onPageChange(current_page + 1)}
          disabled={current_page >= last_page}
          className="rounded-lg p-2 text-slate-400 transition hover:bg-slate-800 hover:text-slate-200 disabled:cursor-not-allowed disabled:opacity-50"
        >
          <ChevronRight className="h-4 w-4" />
        </button>
      </div>
    </div>
  );
}
