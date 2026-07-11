import { cn } from '../../lib/utils';

export function Button({ className, ...props }) {
  return (
    <button
      className={cn(
        'inline-flex items-center justify-center rounded-md bg-cyan-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-cyan-600',
        className
      )}
      {...props}
    />
  );
}
