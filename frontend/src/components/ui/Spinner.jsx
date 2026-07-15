import { cn } from '../../utils/cn';

const sizes = {
  sm: 'h-4 w-4 border-2',
  md: 'h-8 w-8 border-2',
  lg: 'h-12 w-12 border-3',
};

export function Spinner({ className, size = 'md' }) {
  return (
    <div
      className={cn(
        'animate-spin rounded-full border-slate-700 border-t-cyan-400',
        sizes[size],
        className
      )}
    />
  );
}
