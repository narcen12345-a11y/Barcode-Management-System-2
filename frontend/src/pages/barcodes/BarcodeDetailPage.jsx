import { useParams, useNavigate } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { barcodeService } from '../../services/barcodeService';
import { PageHeader } from '../../components/ui/PageHeader';
import { Spinner } from '../../components/ui/Spinner';
import { Pagination } from '../../components/ui/Pagination';

const STATUS_COLORS = {
  NEW: 'text-cyan-400 bg-cyan-500/10',
  OLD: 'text-amber-400 bg-amber-500/10',
};

const STATUS_LABELS = {
  NEW: 'NEW (MOS)',
  OLD: 'OLD (DISMANTLE)',
};

function InfoRow({ label, value }) {
  return (
    <div className="flex items-start gap-4 py-2.5">
      <dt className="w-36 shrink-0 text-sm font-medium text-slate-400">{label}</dt>
      <dd className="text-sm text-slate-200">{value || '—'}</dd>
    </div>
  );
}

function Section({ title, children }) {
  return (
    <div className="rounded-lg border border-slate-700 bg-slate-800/50 p-5">
      <h3 className="mb-4 text-sm font-semibold uppercase tracking-wider text-slate-400">{title}</h3>
      <dl className="divide-y divide-slate-700/50">{children}</dl>
    </div>
  );
}

function TimelineItem({ history }) {
  const getFieldLabel = (field) => {
    const labels = {
      material_id: 'Material',
      site_id: 'Site',
      serial_number: 'Serial Number',
      status: 'Status',
      description: 'Description',
      is_active: 'Is Active',
    };
    return labels[field] || field;
  };

  const formatValue = (field, value) => {
    if (field === 'status') {
      return STATUS_LABELS[value] || value;
    }
    return value || '(kosong)';
  };

  return (
    <div className="relative flex gap-4 pb-6 pl-8 last:pb-0">
      {/* Timeline dot */}
      <div className="absolute left-0 top-1.5 h-3 w-3 rounded-full border-2 border-cyan-500 bg-slate-900" />
      {/* Timeline line */}
      <div className="absolute bottom-0 left-[5px] top-4 w-0.5 bg-slate-700 last:hidden" />

      <div className="flex-1">
        <div className="flex items-center gap-2">
          <span className="text-xs font-medium text-cyan-400">
            {history.changed_by?.full_name || history.changed_by?.username || 'System'}
          </span>
          <span className="text-xs text-slate-500">
            {new Date(history.created_at).toLocaleString('id-ID', {
              year: 'numeric',
              month: 'short',
              day: 'numeric',
              hour: '2-digit',
              minute: '2-digit',
            })}
          </span>
        </div>
        <p className="mt-1 text-sm text-slate-300">
          <span className="font-medium text-slate-200">{getFieldLabel(history.field_name)}</span>
          {' '}berubah dari{' '}
          <span className="text-red-400 line-through">{formatValue(history.field_name, history.old_value)}</span>
          {' '}menjadi{' '}
          <span className="text-green-400">{formatValue(history.field_name, history.new_value)}</span>
        </p>
        {history.change_reason && (
          <p className="mt-0.5 text-xs text-slate-500">Alasan: {history.change_reason}</p>
        )}
      </div>
    </div>
  );
}

export function BarcodeDetailPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [barcode, setBarcode] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // History pagination
  const [historyPage, setHistoryPage] = useState(1);
  const [historyMeta, setHistoryMeta] = useState(null);
  const [histories, setHistories] = useState([]);
  const [loadingHistory, setLoadingHistory] = useState(false);

  // Load barcode detail
  useEffect(() => {
    if (!id) return;
    setLoading(true);
    setError(null);

    barcodeService
      .getById(id)
      .then((res) => {
        const data = res.data || res;
        setBarcode(data);
        // Extract histories from detail resource (loaded with ->load('histories.changedBy'))
        const h = data.histories || [];
        setHistories(h);
        setHistoryMeta(null); // detail resource returns all histories, not paginated
      })
      .catch((err) => {
        setError(err?.response?.data?.message || 'Gagal memuat detail barcode');
      })
      .finally(() => setLoading(false));
  }, [id]);

  // Load paginated history from dedicated endpoint
  const loadHistory = async (page) => {
    setLoadingHistory(true);
    try {
      const res = await barcodeService.getHistory(id, { page, per_page: 10 });
      setHistories(res.data || []);
      setHistoryMeta(res.meta || null);
    } catch {
      // Fallback: if dedicated endpoint fails, keep histories from detail
    } finally {
      setLoadingHistory(false);
    }
  };

  const handleHistoryPageChange = (page) => {
    setHistoryPage(page);
    loadHistory(page);
  };

  // Load paginated history on mount (overrides detail histories)
  useEffect(() => {
    if (id) {
      loadHistory(1);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [id]);

  if (loading) {
    return (
      <div className="flex items-center justify-center py-20">
        <Spinner size="lg" />
      </div>
    );
  }

  if (error) {
    return (
      <div className="flex flex-col items-center justify-center py-20">
        <p className="text-red-400">{error}</p>
        <button
          onClick={() => navigate('/barcodes')}
          className="mt-4 rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800"
        >
          Kembali ke daftar
        </button>
      </div>
    );
  }

  if (!barcode) return null;

  return (
    <div>
      <PageHeader
        title={`Barcode: ${barcode.barcode_id || barcode.serial_number}`}
        description="Detail informasi barcode"
      >
        <div className="flex items-center gap-2">
          <button
            onClick={() => navigate(`/barcodes/${id}/edit`)}
            className="rounded-lg bg-cyan-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-cyan-600"
          >
            Edit
          </button>
          <button
            onClick={() => navigate('/barcodes')}
            className="rounded-lg border border-slate-700 px-4 py-2 text-sm font-medium text-slate-300 transition hover:bg-slate-800"
          >
            Kembali
          </button>
        </div>
      </PageHeader>

      <div className="mt-6 grid gap-6 lg:grid-cols-2">
        {/* Barcode Information */}
        <Section title="Informasi Barcode">
          <InfoRow label="Barcode ID" value={barcode.barcode_id} />
          <InfoRow label="Serial Number" value={barcode.serial_number} />
          <InfoRow
            label="Status"
            value={
              <span
                className={`inline-block rounded-full px-2.5 py-0.5 text-xs font-medium ${
                  STATUS_COLORS[barcode.status] || 'text-slate-400 bg-slate-700'
                }`}
              >
                {STATUS_LABELS[barcode.status] || barcode.status}
              </span>
            }
          />
          <InfoRow label="Description" value={barcode.description} />
          <InfoRow
            label="Active"
            value={
              <span className={barcode.is_active ? 'text-green-400' : 'text-red-400'}>
                {barcode.is_active ? 'Yes' : 'No'}
              </span>
            }
          />
        </Section>

        {/* Material & Site */}
        <Section title="Material & Site">
          <InfoRow
            label="Material"
            value={
              barcode.material
                ? `${barcode.material.name} (${barcode.material.material_code || '-'})`
                : '—'
            }
          />
          <InfoRow
            label="Site"
            value={barcode.site?.name || '—'}
          />
        </Section>

        {/* Created & Updated By */}
        <Section title="Audit Trail">
          <InfoRow
            label="Created By"
            value={barcode.created_by?.full_name || barcode.created_by?.username || '—'}
          />
          <InfoRow
            label="Created At"
            value={
              barcode.created_at
                ? new Date(barcode.created_at).toLocaleString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                  })
                : '—'
            }
          />
          <InfoRow
            label="Updated By"
            value={barcode.updated_by?.full_name || barcode.updated_by?.username || '—'}
          />
          <InfoRow
            label="Updated At"
            value={
              barcode.updated_at
                ? new Date(barcode.updated_at).toLocaleString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                  })
                : '—'
            }
          />
        </Section>

        {/* History / Timeline */}
        <Section title="Riwayat Perubahan">
          {loadingHistory ? (
            <div className="flex items-center justify-center py-8">
              <Spinner size="sm" />
            </div>
          ) : histories.length === 0 ? (
            <p className="py-4 text-center text-sm text-slate-500">Belum ada riwayat perubahan</p>
          ) : (
            <div className="space-y-0">
              {histories.map((h) => (
                <TimelineItem key={h.id} history={h} />
              ))}
            </div>
          )}

          {historyMeta && (
            <div className="mt-4">
              <Pagination meta={historyMeta} onPageChange={handleHistoryPageChange} />
            </div>
          )}
        </Section>
      </div>
    </div>
  );
}
