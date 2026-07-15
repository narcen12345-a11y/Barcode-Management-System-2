import { cn } from '../../utils/cn';

export function EmptyState({ icon: Icon, title, description, action, className }) {
  return (
    <div className={cn('flex flex-col items-center justify-center gap-3 py-20 text-center', className)}>
      {Icon && <Icon className="h-12 w-12 text-slate-600" />}
      <h3 className="text-lg font-medium text-slate-300">{title || 'Tidak ada data'}</h3>
      {description && <p className="max-w-md text-sm text-slate-500">{description}</p>}
      {action && <div className="mt-2">{action}</div>}
    </div>
  );
}
