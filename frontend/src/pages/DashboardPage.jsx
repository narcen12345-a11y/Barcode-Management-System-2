import { useAuth } from '../contexts/AuthContext';
import { PageHeader } from '../components/ui/PageHeader';

// Static color map — no dynamic Tailwind classes
const colorMap = {
  cyan: 'text-cyan-400',
  blue: 'text-blue-400',
  purple: 'text-purple-400',
  emerald: 'text-emerald-400',
};

const stats = [
  { label: 'Total Barcode', value: '—', color: 'cyan' },
  { label: 'Total Material', value: '—', color: 'blue' },
  { label: 'Total Site', value: '—', color: 'purple' },
  { label: 'Total User', value: '—', color: 'emerald' },
];

export function DashboardPage() {
  const { user } = useAuth();

  return (
    <div>
      <PageHeader
        title="Dashboard"
        description={`Selamat datang, ${user?.full_name || user?.username || 'User'}`}
      />

      <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        {stats.map((stat) => (
          <div
            key={stat.label}
            className="rounded-xl border border-slate-800 bg-slate-900/80 p-5"
          >
            <p className="text-sm text-slate-400">{stat.label}</p>
            <p className={`mt-2 text-3xl font-semibold ${colorMap[stat.color] || 'text-slate-400'}`}>
              {stat.value}
            </p>
          </div>
        ))}
      </div>

      <div className="mt-8 rounded-xl border border-slate-800 bg-slate-900/80 p-6">
        <h2 className="text-lg font-semibold text-slate-100">Aktivitas Terbaru</h2>
        <p className="mt-2 text-sm text-slate-500">
          Aktivitas terbaru akan ditampilkan di sini setelah modul CRUD selesai diimplementasikan.
        </p>
      </div>
    </div>
  );
}
