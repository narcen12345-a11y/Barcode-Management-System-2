import { Menu } from 'lucide-react';

export function Topbar({ onMenuClick }) {
  return (
    <header className="flex h-16 items-center gap-4 border-b border-slate-800 bg-slate-900/80 px-6 backdrop-blur-sm">
      <button onClick={onMenuClick} className="text-slate-400 hover:text-slate-200 lg:hidden">
        <Menu className="h-5 w-5" />
      </button>
      <div className="flex-1" />
    </header>
  );
}
