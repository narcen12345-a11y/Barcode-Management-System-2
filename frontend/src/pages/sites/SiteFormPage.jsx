import { useNavigate, useParams } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useFormSubmit } from '../../hooks/useFormSubmit';
import { siteService } from '../../services/siteService';
import { PageHeader } from '../../components/ui/PageHeader';
import { Spinner } from '../../components/ui/Spinner';

const schema = z.object({
  site_id: z.string().min(1, 'Site ID wajib diisi').max(50, 'Maksimal 50 karakter'),
  site_name: z.string().min(1, 'Site Name wajib diisi').max(255, 'Maksimal 255 karakter'),
  region: z.string().max(100, 'Maksimal 100 karakter').optional().or(z.literal('')),
  address: z.string().optional().or(z.literal('')),
  latitude: z.string().max(50, 'Maksimal 50 karakter').optional().or(z.literal('')),
  longitude: z.string().max(50, 'Maksimal 50 karakter').optional().or(z.literal('')),
  is_active: z.boolean().optional().default(true),
});

export function SiteFormPage() {
  const { id } = useParams();
  const isEdit = !!id;
  const navigate = useNavigate();
  const [loadingData, setLoadingData] = useState(isEdit);

  const form = useFormSubmit({
    service: siteService,
    method: isEdit ? 'update' : 'create',
    id,
    queryKey: 'sites',
    onSuccess: () => navigate('/sites'),
  });

  const {
    register,
    handleSubmit,
    formState: { errors },
    reset,
    watch,
  } = useForm({
    resolver: zodResolver(schema),
    defaultValues: {
      is_active: true,
    },
  });

  const isActive = watch('is_active');

  // Load existing data for edit
  useEffect(() => {
    if (isEdit) {
      siteService
        .getById(id)
        .then((res) => {
          const data = res.data || res;
          reset({
            site_id: data.site_id || '',
            site_name: data.site_name || '',
            region: data.region || '',
            address: data.address || '',
            latitude: data.latitude || '',
            longitude: data.longitude || '',
            is_active: data.is_active ?? true,
          });
        })
        .finally(() => setLoadingData(false));
    }
  }, [id, isEdit, reset]);

  const onSubmit = async (data) => {
    await form.submit(data);
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
        title={isEdit ? 'Edit Site' : 'Tambah Site'}
        description={isEdit ? 'Perbarui data site' : 'Buat data site baru'}
      />

      <form onSubmit={handleSubmit(onSubmit)} className="max-w-2xl space-y-5">
        {/* Site ID */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Site ID <span className="text-red-400">*</span>
          </label>
          <input
            {...register('site_id')}
            placeholder="Contoh: SITE-001"
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.site_id && (
            <p className="mt-1 text-xs text-red-400">{errors.site_id.message}</p>
          )}
          {form.errors?.site_id && (
            <p className="mt-1 text-xs text-red-400">{form.errors.site_id}</p>
          )}
        </div>

        {/* Site Name */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Site Name <span className="text-red-400">*</span>
          </label>
          <input
            {...register('site_name')}
            placeholder="Nama site / lokasi"
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.site_name && (
            <p className="mt-1 text-xs text-red-400">{errors.site_name.message}</p>
          )}
          {form.errors?.site_name && (
            <p className="mt-1 text-xs text-red-400">{form.errors.site_name}</p>
          )}
        </div>

        {/* Region */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">Region</label>
          <input
            {...register('region')}
            placeholder="Contoh: Jawa Timur"
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.region && (
            <p className="mt-1 text-xs text-red-400">{errors.region.message}</p>
          )}
          {form.errors?.region && (
            <p className="mt-1 text-xs text-red-400">{form.errors.region}</p>
          )}
        </div>

        {/* Address */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">Address</label>
          <textarea
            {...register('address')}
            rows={3}
            placeholder="Alamat lengkap site"
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.address && (
            <p className="mt-1 text-xs text-red-400">{errors.address.message}</p>
          )}
          {form.errors?.address && (
            <p className="mt-1 text-xs text-red-400">{form.errors.address}</p>
          )}
        </div>

        {/* Latitude & Longitude */}
        <div className="grid grid-cols-2 gap-4">
          <div>
            <label className="mb-1.5 block text-sm font-medium text-slate-300">Latitude</label>
            <input
              {...register('latitude')}
              placeholder="-7.250445"
              className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
            />
            {errors.latitude && (
              <p className="mt-1 text-xs text-red-400">{errors.latitude.message}</p>
            )}
            {form.errors?.latitude && (
              <p className="mt-1 text-xs text-red-400">{form.errors.latitude}</p>
            )}
          </div>
          <div>
            <label className="mb-1.5 block text-sm font-medium text-slate-300">Longitude</label>
            <input
              {...register('longitude')}
              placeholder="112.744444"
              className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
            />
            {errors.longitude && (
              <p className="mt-1 text-xs text-red-400">{errors.longitude.message}</p>
            )}
            {form.errors?.longitude && (
              <p className="mt-1 text-xs text-red-400">{form.errors.longitude}</p>
            )}
          </div>
        </div>

        {/* Is Active */}
        <div>
          <label className="flex items-center gap-3">
            <input
              type="checkbox"
              {...register('is_active')}
              className="h-4 w-4 rounded border-slate-700 bg-slate-800 text-cyan-500 focus:ring-cyan-500"
            />
            <span className="text-sm font-medium text-slate-300">Active</span>
          </label>
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
            onClick={() => navigate('/sites')}
            className="rounded-lg border border-slate-700 px-6 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-slate-800"
          >
            Batal
          </button>
        </div>
      </form>
    </div>
  );
}
