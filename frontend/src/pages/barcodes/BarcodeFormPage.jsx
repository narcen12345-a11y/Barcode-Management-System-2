import { useNavigate, useParams } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useFormSubmit } from '../../hooks/useFormSubmit';
import { barcodeService } from '../../services/barcodeService';
import { siteService } from '../../services/siteService';
import { materialService } from '../../services/materialService';
import { PageHeader } from '../../components/ui/PageHeader';
import { Spinner } from '../../components/ui/Spinner';

// Validation must match backend FormRequest exactly:
// StoreBarcodeRequest: material_id (required|exists), site_id (required|exists),
//   serial_number (required|max:255|unique), status (required|Enum:NEW,OLD), description (nullable)
// UpdateBarcodeRequest: same fields but all "sometimes" + serial_number unique ignores own ID
const schema = z.object({
  material_id: z.string().min(1, 'Material wajib dipilih'),
  site_id: z.string().min(1, 'Site wajib dipilih'),
  serial_number: z.string().min(1, 'Serial Number wajib diisi').max(255, 'Maksimal 255 karakter'),
  status: z.string().min(1, 'Status wajib dipilih'),
  description: z.string().optional().or(z.literal('')),
});

const STATUS_OPTIONS = [
  { value: 'NEW', label: 'NEW (MOS)' },
  { value: 'OLD', label: 'OLD (DISMANTLE)' },
];

export function BarcodeFormPage() {
  const { id } = useParams();
  const isEdit = !!id;
  const navigate = useNavigate();
  const [loadingData, setLoadingData] = useState(isEdit);

  // Dropdown data
  const [sites, setSites] = useState([]);
  const [materials, setMaterials] = useState([]);
  const [loadingSites, setLoadingSites] = useState(true);
  const [loadingMaterials, setLoadingMaterials] = useState(true);

  const form = useFormSubmit({
    service: barcodeService,
    method: isEdit ? 'update' : 'create',
    id,
    queryKey: 'barcodes',
    onSuccess: () => navigate('/barcodes'),
  });

  const {
    register,
    handleSubmit,
    formState: { errors },
    reset,
  } = useForm({
    resolver: zodResolver(schema),
    defaultValues: {
      material_id: '',
      site_id: '',
      serial_number: '',
      status: '',
      description: '',
    },
  });

  // Load dropdown data on mount
  useEffect(() => {
    siteService
      .getAllUnpaginated()
      .then((res) => {
        setSites(res.data || res || []);
      })
      .finally(() => setLoadingSites(false));

    materialService
      .getAllUnpaginated()
      .then((res) => {
        setMaterials(res.data || res || []);
      })
      .finally(() => setLoadingMaterials(false));
  }, []);

  // Load existing data for edit
  useEffect(() => {
    if (isEdit) {
      barcodeService
        .getById(id)
        .then((res) => {
          const data = res.data || res;
          reset({
            material_id: String(data.material_id || data.material?.id || ''),
            site_id: String(data.site_id || data.site?.id || ''),
            serial_number: data.serial_number || '',
            status: data.status || '',
            description: data.description || '',
          });
        })
        .finally(() => setLoadingData(false));
    }
  }, [id, isEdit, reset]);

  const onSubmit = async (data) => {
    await form.submit({
      material_id: Number(data.material_id),
      site_id: Number(data.site_id),
      serial_number: data.serial_number,
      status: data.status,
      description: data.description || null,
    });
  };

  if (loadingData) {
    return (
      <div className="flex items-center justify-center py-20">
        <Spinner size="lg" />
      </div>
    );
  }

  return (
    <div>
      <PageHeader
        title={isEdit ? 'Edit Barcode' : 'Tambah Barcode'}
        description={isEdit ? 'Perbarui data barcode' : 'Buat data barcode baru'}
      />

      <form onSubmit={handleSubmit(onSubmit)} className="max-w-2xl space-y-5">
        {/* Material */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Material <span className="text-red-400">*</span>
          </label>
          {loadingMaterials ? (
            <div className="flex items-center gap-2 text-sm text-slate-500">
              <Spinner size="sm" /> Memuat material...
            </div>
          ) : (
            <select
              {...register('material_id')}
              className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
            >
              <option value="">Pilih Material</option>
              {materials.map((m) => (
                <option key={m.id} value={m.id}>
                  {m.name} ({m.material_code || m.id})
                </option>
              ))}
            </select>
          )}
          {errors.material_id && (
            <p className="mt-1 text-xs text-red-400">{errors.material_id.message}</p>
          )}
          {form.errors?.material_id && (
            <p className="mt-1 text-xs text-red-400">{form.errors.material_id}</p>
          )}
        </div>

        {/* Site */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Site <span className="text-red-400">*</span>
          </label>
          {loadingSites ? (
            <div className="flex items-center gap-2 text-sm text-slate-500">
              <Spinner size="sm" /> Memuat site...
            </div>
          ) : (
            <select
              {...register('site_id')}
              className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
            >
              <option value="">Pilih Site</option>
              {sites.map((s) => (
                <option key={s.id} value={s.id}>
                  {s.name}
                </option>
              ))}
            </select>
          )}
          {errors.site_id && (
            <p className="mt-1 text-xs text-red-400">{errors.site_id.message}</p>
          )}
          {form.errors?.site_id && (
            <p className="mt-1 text-xs text-red-400">{form.errors.site_id}</p>
          )}
        </div>

        {/* Serial Number */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Serial Number <span className="text-red-400">*</span>
          </label>
          <input
            {...register('serial_number')}
            placeholder="Nomor seri barcode"
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.serial_number && (
            <p className="mt-1 text-xs text-red-400">{errors.serial_number.message}</p>
          )}
          {form.errors?.serial_number && (
            <p className="mt-1 text-xs text-red-400">{form.errors.serial_number}</p>
          )}
        </div>

        {/* Status */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Status <span className="text-red-400">*</span>
          </label>
          <select
            {...register('status')}
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          >
            <option value="">Pilih Status</option>
            {STATUS_OPTIONS.map((opt) => (
              <option key={opt.value} value={opt.value}>
                {opt.label}
              </option>
            ))}
          </select>
          {errors.status && (
            <p className="mt-1 text-xs text-red-400">{errors.status.message}</p>
          )}
          {form.errors?.status && (
            <p className="mt-1 text-xs text-red-400">{form.errors.status}</p>
          )}
        </div>

        {/* Description */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">Description</label>
          <textarea
            {...register('description')}
            rows={3}
            placeholder="Deskripsi barcode"
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.description && (
            <p className="mt-1 text-xs text-red-400">{errors.description.message}</p>
          )}
          {form.errors?.description && (
            <p className="mt-1 text-xs text-red-400">{form.errors.description}</p>
          )}
        </div>

        {/* Actions */}
        <div className="flex items-center gap-3 pt-2">
          <button
            type="submit"
            disabled={form.loading}
            className="inline-flex items-center gap-2 rounded-lg bg-cyan-500 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-cyan-600 disabled:cursor-not-allowed disabled:opacity-50"
          >
            {form.loading ? (
              <>
                <Spinner size="sm" />
                Menyimpan...
              </>
            ) : (
              'Simpan'
            )}
          </button>
          <button
            type="button"
            onClick={() => navigate('/barcodes')}
            className="rounded-lg border border-slate-700 px-6 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-slate-800"
          >
            Batal
          </button>
        </div>
      </form>
    </div>
  );
}
